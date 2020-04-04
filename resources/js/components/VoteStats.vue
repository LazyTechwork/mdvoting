<template>
    <div>

    </div>
</template>

<script>
    export default {
        name: "VoteStats",
        props: ["vid", "vicode"],
        data: function () {
            return {
                votes: [],
                variants: []
            };
        },
        mounted() {
            Echo.connect();
            Echo.channel("mdvoting_"+this.vicode).listen(".newvote", (e)=>{
                this.variants = e.voting.variants;
                this.votes = e.votes;
            });
        }
    }
</script>

