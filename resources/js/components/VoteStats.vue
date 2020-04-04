<template>
    <div>
        <canvas id="voteChart" width="400" height="400" ref="voteChart"></canvas>
    </div>
</template>

<script>
    import Chart from 'chart.js';

    export default {
        name: "VoteStats",
        props: ["vid", "vicode"],
        data: function () {
            return {
                votes: [],
                variants: [],
                canvas: null,
                chart: null
            };
        },
        mounted() {
            Echo.connect();
            Echo.channel("mdvoting_" + this.vicode).listen(".newvote", (e) => {
                this.variants = e.voting.variants;
                this.votes = e.votes;
            });
            this.canvas = this.$refs.voteChart;
            axios.get('/gv', {params: {v: this.vid}}).then(response => {
                if (response.data.status==='ok'){
                    this.votes = response.data.votes;
                    this.variants = response.data.voting.variants;
                }
            });
        },
        watch: {
            votes: function (val) {
                this.chart = new Chart(this.canvas, {
                    type: 'doughnut',
                    data: {
                        labels: this.variants,
                        datasets: [{
                            data: val
                        }]
                    }
                });
            }
        }
    }
</script>

