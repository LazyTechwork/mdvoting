<template>
    <div class="h-100">
        <div class="d-flex flex-column align-items-center justify-content-center h-100">
            <div class="col-md-6 text-center">
                <h2 class="font-weight-bold">Система Vi</h2>
                <h4 class="mb-4">Подключите устройство к голосованию</h4>
                <div class="input-group">
                    <input type="text" class="form-control form-control-lg text-center font-weight-bold text-uppercase"
                           v-model="code"
                           aria-label="КОД ГОЛОСОВАНИЯ" placeholder="Код голосования" maxlength="6">
                    <div class="input-group-append">
                        <button class="btn btn-lg font-weight-bold btn-outline-primary text-uppercase">Подключиться
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "ViComponent",
        data: function () {
            return {
                code: null,

            };
        },
        watch: {
            code: function (value) {
                if (value.length > 6)
                    value = value.substr(0, 6);

                if (!(/^[a-zA-Z0-9]+$/gm.test(value)))
                    value = value.replace(/[^a-zA-Z0-9]/g, '');

                this.code = value.toUpperCase();
            }
        }
    }
</script>

