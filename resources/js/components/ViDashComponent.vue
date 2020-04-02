<template>
    <div>
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
                    <button class="btn btn-outline-primary w-100">Направление</button>
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
        data: function () {
            return {
                devices: [{
                    uuid: 'adssa',
                    name: 'Стол №1',
                    status: 'free',
                }]
            };
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
            }
        }
    }
</script>
