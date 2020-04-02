<template>
    <div>
        <form action="#" method="GET" id="selectuser" class="mb-5">
            <div class="form-group" v-if="pgroups">
                <label for="groupselect">Выберите группу</label>
                <select type="text" class="form-control" id="groupselect" v-model="selectedgroup">
                    <option :value="null">Не выбрано</option>
                    <option v-for="pg in pgroups" :value="pg" :key="pg">{{ pg }}</option>
                </select>
            </div>
            <div class="form-group" v-if="ps && selectedgroup">
                <label for="participantselect">Выберите участника</label>
                <select type="text" class="form-control" id="participantselect" v-model="selected">
                    <option :value="null">Не выбрано</option>
                    <option v-for="p in ps[selectedgroup]" :value="p" :key="p.id">{{ p.name }}</option>
                </select>
            </div>
        </form>
        <table class="table table-responsive-md">
            <thead class="thead-dark">
            <tr>
                <th>Устройство</th>
                <th>Статус</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="d in devices" :key="d.uuid">
                <td>{{ d.name }}</td>
                <td v-html="parseStatus(d.status)"></td>
                <td>
                    <button class="btn btn-outline-primary w-100" :disabled="!selected">Направить на это устройство</button>
                    <button class="btn btn-outline-danger w-100">Удалить устройство</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
    export default {
        name: "ViDashComponent",
        props: ['vid'],
        data: function () {
            return {
                devices: [{
                    uuid: 'adssa',
                    name: 'Стол №1',
                    status: 'free'
                }],
                selected: null,
                selectedgroup: null,
                ps: [],
                pgroups: []
            };
        },
        mounted() {
            axios.get('/gp', {params: {v: this.vid}}).then(function (response) {
                this.ps = response.data.items;
                this.pgroups = response.data.itemgroups;
            }.bind(this)).catch(error => console.error(error));
        },
        methods: {
            parseStatus(status) {
                switch (status) {
                    case 'free':
                        return '<span class="badge badge-success" style="font-size: 1rem;">Свободно</span>';
                    case 'voting':
                        return '<span class="badge badge-warning" style="font-size: 1rem;">Голосует</span>';
                    default:
                        return '<span class="badge badge-secondary" style="font-size: 1rem;">Неизвестный статус</span>';
                }
            },
            test() {
                axios.get('/gp', {params: {v: this.vid}}).then(function (response) {
                    this.ps = response.data.items;
                    this.psgroups = response.data.itemgroups;
                }.bind(this)).catch(error => console.error(error));
            }
        }
    }
</script>
