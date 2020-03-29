<template>
    <div>
        <table class="table table-responsive-md">
            <thead class="thead-dark">
            <tr>
                <th>Вариант</th>
                <th>Действия</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(v, i) in variants" :key="i">
                <td><input type="text" v-model="variants[i]" class="form-control"></td>
                <td>
                    <button class="btn btn-outline-danger w-100" @click="removeVariant(i)">Удалить</button>
                </td>
            </tr>
            </tbody>
        </table>
        <form :action="$props.sendroute" method="POST" class="d-none" ref="sendform">
            <input type="hidden" name="_token" :value="csrf">
            <input type="hidden" name="variants[]" v-for="(v,i) in processedVariants" :value="v" :key="i">
        </form>
        <button class="btn btn-outline-primary" @click="send">Сохранить варианты голосования</button>
    </div>
</template>

<script>
    export default {
        name: "VariantsEdit",
        props: ['vs', 'sendroute', 'csrf'],
        data: function () {
            return {
                variants: [],
                processedVariants: []
            };
        },
        mounted() {
            this.variants = JSON.parse(this.$props.vs);
            if (this.variants.length === 0)
                this.variants.push('');
        },
        methods: {
            removeVariant(i) {
                this.variants.splice(i, 1);
            },
            send() {
                let vs = [];
                for (let v of this.variants) {
                    if (v)
                        vs.push(v);
                }
                this.processedVariants = vs;
                this.$refs.sendform.submit();
            }
        },
        watch: {
            variants: function (val) {
                if (val.length === 0 || val[val.length - 1])
                    this.variants.push('');
            }
        }
    }
</script>

<style scoped>

</style>
