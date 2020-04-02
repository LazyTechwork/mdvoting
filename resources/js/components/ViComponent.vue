<template>
    <div class="h-100">
        <transition name="custom-classes-transition"
                    enter-active-class="animated fast bounceIn"
                    leave-active-class="animated fast bounceOut"
                    mode="out-in">
            <div class="col-md-6 mx-auto text-center h-100" v-if="screen === 'intro'"
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
                                @click="connect">
                            Подключиться
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 flex-center mx-auto text-center h-100" v-if="screen === 'setup'"
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
                                @click="setup">
                            Сохранить
                        </button>
                    </div>
                </div>
            </div>

            <div class="col-md-6 mx-auto text-center h-100" v-if="screen === 'wait'"
                 :key="screen">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">Устройство подключено к голосованию (<b>{{ this.code }}</b>)</h4>
                <h5 class="text-secondary">Подождите, когда оператор начнёт голосование</h5>
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
                devicename: ''
            };
        },
        mounted() {
            if (localStorage.getItem('vi_code')) {
                this.code = localStorage.getItem('vi_code');
                this.devicename = localStorage.getItem('vi_devicename') ? localStorage.getItem('vi_devicename') : '';
                this.connect();
            } else {
                this.screen = 'intro';
            }
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
            connect: function () {
                localStorage.setItem('vi_code', this.code);
                this.screen = 'setup';
            },
            setup: function () {
                if (this.devicename.length === 0)
                    return;
                localStorage.setItem('vi_devicename', this.devicename);
                this.screen = 'wait';
            }
        }
    }
</script>

