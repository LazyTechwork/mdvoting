<script>
    import {Doughnut, mixins} from 'vue-chartjs'
    import 'chartjs-plugin-colorschemes/src/plugins/plugin.colorschemes';
    import {Aspect6} from 'chartjs-plugin-colorschemes/src/colorschemes/colorschemes.office';

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
            this.addPlugin({
                id: "plugin.colorschemes",
                scheme: Aspect6
            });
            this.renderChart(this.chartData, this.options)
        }
    }
</script>

<style scoped>

</style>
