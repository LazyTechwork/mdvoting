<script>
    import {Doughnut, mixins} from 'vue-chartjs'
    import 'chartjs-plugin-colorschemes';

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
                maintainAspectRatio: false,
                legend: {
                    position: 'bottom'
                },
                tooltips: {
                    callbacks: {
                        label: function (tooltipItem, data) {
                            //get the concerned dataset
                            let dataset = data.datasets[tooltipItem.datasetIndex];
                            //calculate the total of this data set
                            let total = dataset.data.reduce(function (previousValue, currentValue, currentIndex, array) {
                                return previousValue + currentValue;
                            });
                            //get the current items value
                            let currentValue = dataset.data[tooltipItem.index];
                            //calculate the precentage based on the total and current item, also this does a rough rounding to give a whole number
                            let percentage = Math.floor(((currentValue / total) * 100) + 0.5);

                            return currentValue + " (" + percentage + "%)";
                        }
                    }
                }
            }
        }),
        mixins: [mixins.reactiveProp],
        mounted() {
            this.addPlugin({
                colorschemes: {
                    scheme: 'brewer.PRGn11'
                }
            });
            this.renderChart(this.chartData, this.options)
        }
    }
</script>

<style scoped>

</style>
