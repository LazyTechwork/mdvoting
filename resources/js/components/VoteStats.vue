<template>
    <div>
        <statschart :chartData="chartData"></statschart>
        <!--<statschart v-if="variants && votes" :chartData="{
                    labels: ['Test 1', 'Test 2'],
                    datasets: [{
                        data: [2, 5]
                    }]
                }"></statschart>-->
    </div>
</template>

<script>
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
            axios.get('/gv', {params: {v: this.vid}}).then(response => {
                if (response.data.status === 'ok') {
                    this.votes = response.data.votes;
                    this.variants = response.data.voting.variants;
                }
            });
        },
        computed: {
            chartData: function () {
                window.dispatchEvent(new Event('resize'));
                return {
                    labels: this.variants,
                    datasets: [{
                        data: this.votes,
                    }]
                };
            }
        }
    }
</script>

