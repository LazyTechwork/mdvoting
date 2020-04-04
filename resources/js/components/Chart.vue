<script>
    import {Doughnut, mixins} from 'vue-chartjs'

    export default {
        extends: Doughnut,
        name: "Chart",
        props: {
            chartData: {
                type: Object,
                default: null
            }
        },
        data: () => ({
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        }),
        mixins: [mixins.reactiveProp],
        mounted() {
            this.renderChart(this.chartData, this.options)
        }
    }
</script>

<style scoped>

</style>
