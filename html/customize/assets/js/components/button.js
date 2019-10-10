Vue.component("ui-button", {
    props: ["text", "submitCallback", "submitCallbackArgs"],
    data: function() {
        return {
            submitted: false
        };
    },
    methods: {
        submit: function() {
            let self = this;
            self.submitted = true;
            if (typeof this.submitCallback === 'function') {
                if (typeof this.submitCallbackArgs === 'object') {
                    self.submitCallback(this.submitCallbackArgs, function() {
                        self.submitted = false;
                    });
                } else {
                    self.submitCallback(function() {
                        self.submitted = false;
                    });
                }
            }
        }
    }
});