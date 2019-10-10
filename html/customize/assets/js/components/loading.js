Vue.component("ui-loading", {
    props: {
        loading: {type: Boolean, default: false},
        iconClassName: {type: String, default: "fa-2x"},
        display: {type: String, default: "container"}
    },
    data: function() {
        return {
            id: 'ui-loading-' + Math.random(),
            width: 0,
            height: 0
        };
    },
    template: '<div :id="id">' +
                '<div v-show="loading && display === \'container\'" class="blockOverlay" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100%; top: 0px; left: 0px; background-color: rgb(255, 255, 255); opacity: 0.6; cursor: wait; position: absolute;"></div>' +
                '<div v-show="loading && display === \'container\'" class="blockElement" style="z-index: 1011; position: absolute; padding: 0px; margin: 0px; width: 30%; text-align: center; color: rgb(0, 0, 0); border: 0px; cursor: wait;" :style="{top: height + \'px\', left: width + \'px\'}"><i class="fas fa-spin fa-cube text-dark" :class="iconClassName"></i></div>' +
                '<div v-show="loading && display === \'fullscreen\'" class="blockOverlay" style="z-index: 1000; border: none; margin: 0px; padding: 0px; width: 100%; height: 100vh; top: 0px; left: 0px; background-color: rgb(255, 255, 255); opacity: 0.6; cursor: wait; position: fixed;"></div>' +
                '<div v-show="loading && display === \'fullscreen\'" class="blockElement" style="z-index: 1011; position: fixed; padding: 0px; margin: 0px; width: 100%; height:100vh;text-align: center; color: rgb(0, 0, 0); border: 0px; cursor: wait;top:0px;left:0px;display: flex;justify-content: center;align-items: center"><i class="fas fa-spin fa-cube text-dark" :class="iconClassName"></i></div>' +
        '</div>',
    mounted: function () {
        if (this.display === "container") {
            const container = document.getElementById(this.id).parentElement;
            this.width = container.offsetWidth/3;
            this.height = container.offsetHeight/2 - 14;
        }
    },
    beforeUpdate: function () {
        if (this.display === "container") {
            const container = document.getElementById(this.id).parentElement;
            this.width = container.offsetWidth/3;
            this.height = container.offsetHeight/2 - 14;
        }
    }
});