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
            <tr>
                <td>УСТРОЙСТВО</td>
                <td></td>
                <td><a href="{{ route('votings.edit', ['id' => $voting->id]) }}" class="btn btn-outline-primary w-100">Изменить</a>
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
                devices: []
            };
        }
    }
</script>
