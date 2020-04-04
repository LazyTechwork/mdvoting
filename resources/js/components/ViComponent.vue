<template>
    <div class="h-100">
        <transition name="animatecss"
                    enter-active-class="animated fast bounceIn"
                    leave-active-class="animated fast bounceOut"
                    mode="out-in">
            <div class="col-md-6 flex-center mx-auto text-center h-100" v-if="screen === 'intro' && !loading"
                 :key="screen">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">Подключите устройство к голосованию</h4>
                <div class="input-group">
                    <input type="text"
                           class="form-control form-control-lg text-center font-weight-bold text-uppercase"
                           v-model="code"
                           aria-label="КОД ГОЛОСОВАНИЯ" placeholder="Код голосования" maxlength="6">
                    <div class="input-group-append">
                        <button class="btn btn-lg font-weight-bold btn-outline-primary text-uppercase"
                                @click="connect()">
                            Подключиться
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 flex-center mx-auto text-center h-100" v-if="screen === 'setup' && !loading"
                 :key="screen">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">Устройство подключено к голосованию (<b>{{ this.code }}</b>)</h4>
                <div class="input-group">
                    <input type="text"
                           class="form-control form-control-lg text-center"
                           v-model="devicename"
                           aria-label="Название устройства" placeholder="Название устройства" maxlength="20">
                    <div class="input-group-append">
                        <button class="btn btn-lg font-weight-bold btn-outline-primary text-uppercase"
                                @click="setup()">
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 flex-center mx-auto text-center h-100" v-if="screen === 'wait' && !loading"
                 :key="screen">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">Устройство подключено к голосованию (<b>{{ this.code }}</b>)</h4>
                <h5 class="text-secondary">Подождите, когда оператор начнёт голосование</h5>
            </div>

            <div class="col-md-7 flex-center mx-auto text-center h-100"
                 v-if="screen === 'approvalwait' && !loading && participant"
                 :key="screen">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">К устройству подключён голосующий <b>{{ participant.name }}</b> ({{ participant.group
                    }})</h4>
                <button class="w-100 btn btn-outline-primary" @click="startvoting">Начать голосование</button>
            </div>

            <div class="col-md-7 flex-center mx-auto text-center h-100"
                 v-if="screen === 'voting' && !loading && variants && participant"
                 :key="screen">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">Выберите варианты</h4>
                <form action="#" @submit.prevent="vote" class="w-100">
                    <div class="varcheck" style="user-select: none;"
                         v-for="(vars,i) in variants">
                        <input type="checkbox" :id="'varcheck_'+i"
                               :disabled="selectBlock(i)" :value="i" v-model="selected_variants">
                        <label class="h2" :for="'varcheck_'+i">{{ vars }}</label>
                    </div>
                    <button type="submit" class="w-100 btn btn-outline-primary" @click="vote">Проголосовать</button>
                </form>
            </div>
        </transition>
        <transition name="animatecss"
                    enter-active-class="animated faster fadeIn"
                    leave-active-class="animated faster fadeOut">
            <div class="loader" v-if="loading">
                <h1>Идёт загрузка</h1>
            </div>
        </transition>
    </div>
</template>

<script>
    export default {
        name: "ViComponent",
        data: function () {
            return {
                code: null,
                screen: '',
                devicename: '',
                deviceid: null,
                loading: false,
                participant: null,
                variants: null,
                maxvotes: 0,
                selected_variants: []
            };
        },
        mounted() {
            if (localStorage.getItem('vi_code')) {
                this.code = localStorage.getItem('vi_code');
                if (localStorage.getItem('vi_devicename')) {
                    this.devicename = localStorage.getItem('vi_devicename');
                    this.connect(true);
                } else {
                    this.connect();
                }
            } else {
                this.screen = 'intro';
            }
        },
        created() {
            window.addEventListener('beforeunload', this.unlinkdevice);
        },
        watch: {
            code: function (value) {
                if (value.length > 6)
                    value = value.substr(0, 6);

                if (!(/^[a-zA-Z0-9]+$/gm.test(value)))
                    value = value.replace(/[^a-zA-Z0-9]/g, '');

                this.code = value.toUpperCase();
            },
            devicename: function (value) {
                if (value.length > 20)
                    this.devicename = value.substr(0, 20);
            }
        },
        methods: {
            connect: function (withoutSetup = false) {
                localStorage.setItem('vi_code', this.code);
                if (withoutSetup) {
                    this.setup();
                    return;
                }
                this.screen = 'setup';
            },
            setup: function () {
                if (this.devicename.length === 0)
                    return;
                localStorage.setItem('vi_devicename', this.devicename);
                this.loading = true;
                axios.post('/cd', {
                    vi_code: this.code,
                    vi_name: this.devicename
                }).then(function (response) {
                    if (response.data.status === 'ok') {
                        this.loading = false;
                        this.screen = 'wait';
                        this.deviceid = response.data.item.id;
                        this.bindlisteners();
                    }
                }.bind(this), function (error) {
                    let response = error.response;
                    if (response.data.status === 'notfound') {
                        this.loading = false;
                        this.clearvinfo();
                        this.screen = 'intro';
                    }
                }.bind(this));
            },
            startvoting: function () {
                if (!this.participant)
                    return;
                this.loading = true;
                axios.post('/sv', {
                    v: this.code,
                    d: this.deviceid
                }).then(function (response) {
                    if (response.data.status === 'ok') {
                        this.variants = response.data.items;
                        this.maxvotes = response.data.maxvotes;
                        this.screen = 'voting';
                        this.loading = false;
                    }
                }.bind(this), function (error) {
                    let response = error.response;
                    if (response.data.status === 'notfound') {
                        this.loading = false;
                        this.clearvinfo();
                        this.screen = 'intro';
                    }
                }.bind(this));
            },
            vote: function () {
                if (!this.participant || !this.selected_variants)
                    return;
                this.loading = true;
                axios.post('/ev', {
                    v: this.code,
                    d: this.deviceid,
                    p: this.participant.id,
                    vote: this.selected_variants
                }).then(function (response) {
                    if (response.data.status === 'ok') {
                        this.variants = null;
                        this.maxvotes = 0;
                        this.participant = null;
                        this.selected_variants = [];
                        this.screen = 'wait';
                        this.loading = false;
                    }
                }.bind(this), function (error) {
                    let response = error.response;
                    if (response.data.status === 'notfound') {
                        this.loading = false;
                        this.clearvinfo();
                        this.screen = 'intro';
                    }
                }.bind(this));
            },
            bindlisteners: function () {
                Echo.connect();
                Echo.channel("mdvoting_" + this.code).listen('.participantlinked', (e) => {
                    if (e.device.id === this.deviceid) {
                        this.participant = e.participant;
                        this.screen = 'approvalwait';
                    }
                });
                Echo.channel("mdvoting_" + this.code).listen('.deviceunlink', (e) => {
                    if (e.device.id === this.deviceid) {
                        this.clearvinfo();
                        this.screen = 'intro';
                        Echo.disconnect();
                    }
                });
            },
            clearvinfo: function () {
                localStorage.removeItem('vi_code');
                localStorage.removeItem('vi_devicename');
                this.devicename = '';
                this.code = '';
            },
            selectBlock: function (id) {
                return this.selected_variants.length >= this.maxvotes && !this.selected_variants.includes(id);
            },
            unlinkdevice: function () {
                axios.post('/devdis', {v: this.code, d: this.deviceid}).then(function (e) {
                    window.location.reload();
                }.bind(this), (e) => {
                });
            }
        }
    }
</script>
