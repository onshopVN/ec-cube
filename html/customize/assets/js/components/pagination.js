Vue.component("ui-pagination", {
    props: ["pageSize", "pageNum", "totals", "pageRange", "submitCallback"],
    data: function () {
        return {

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
            if (page.hasOwnProperty('actualNumber')) {
                page.number = page.actualNumber;
            }
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

            if (i > 1) {
                pages.push({
                    number: 1,
                    active: false,
                    disabled: false,
                    className: ''
                });
                if ((i-1) > 1) {
                    let actualNumber = (pageNum - this.pageRange) < 1 ? 2  : pageNum - this.pageRange;
                    pages.push({
                        number: '...',
                        actualNumber: actualNumber,
                        active: false,
                        disabled: true,
                        className: ''
                    });
                }
            }

            for (i;i<=pageMax;i++) {
                if (i > 0 && i <= pageCount) {
                    pages.push({
                        number: i,
                        active: i === pageNum,
                        disabled: i === pageNum,
                        className: (i === pageNum) ? 'active' : ''
                    });
                }
            }

            if (pageMax < pageCount) {
                if (pageMax+1 < pageCount) {
                    let actualNumber = (pageNum + pageMax) > pageCount ? pageCount - 1  : pageNum + pageMax;
                    pages.push({
                        number: '...',
                        actualNumber: actualNumber,
                        active: false,
                        disabled: true,
                        className: ''
                    });
                }
                pages.push({
                    number: pageCount,
                    active: false,
                    disabled: false,
                    className: ''
                });
            }

            return pages;
        }
    }
});