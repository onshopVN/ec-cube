Vue.component("ui-pagination", {
    props: ["pageSize", "pageNum", "totals", "pageRange", "submitCallback"],
    data: function () {
        return {
            loading: 0
        };
    },
    template: '<ul class="pagination justify-content-center my-3" v-show="isVisible">' +
                '<li class="page-item" :class="page.className" :disabled="page.disabled" v-for="page in pages">' +
                    '<button class="page-link" @click.stop.prevent="submit(page)">' +
                        '<span v-text="page.number"></span>' +
                    '</button>' +
                '</li>' +
        '</ul>',
    methods: {
        submit: function (page) {
            this.loading = page.number;
            if (typeof this.submitCallback === 'function') {
                this.submitCallback(page);
            }
        }
    },
    computed: {
        isVisible: function() {
            return this.pageCount > 1;
        },
        pageCount: function() {
            return Math.ceil(parseInt(this.totals)/parseInt(this.pageSize));
        },
        pages: function () {
            let pages = [];
            let pageCount = this.pageCount;
            let pageNum = parseInt(this.pageNum);
            let pageMax = pageNum + parseInt(this.pageRange);
            let i = pageNum - this.pageRange;
            for (i;i<=pageMax;i++) {
                if (i > 0 && i <= pageCount) {
                    pages.push({
                        number: i,
                        active: i === pageNum,
                        disabled: i === pageNum,
                        className: (i === pageNum) ? 'active' : '',
                        showLoading: (this.loading === i && i !== pageNum)
                    });
                }
            }
            return pages;
        }
    }
});