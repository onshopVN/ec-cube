class Search extends React.Component {
    constructor(props) {
        super(props);
        this.components = props.components;
        this.pageNum = props.data.pageNum || 0;
        this.pageSize = props.data.pageSize || 0;
        this.state = {
            loading: false,
            keyword: ""
        };
        this.submit = this.submit.bind(this);
    }

    update (data) {
        if (data.hasOwnProperty("pageNum")) {
            this.pageNum = data.pageNum;
        }
    }

    submit (e) {
        e.preventDefault();
        let self = this;
        self.setState(state => ({
            loading: true
        }));

        let categories = $.map($('input[name="search[category][]"]:checked'), function(c){return c.value; });
        let keyword = $('input[name="search[keyword]"]').val();
        $.ajax({
            type: "POST",
            url: t["admin_store_plugin_owners_ajax"],
            data: {
                categories: categories,
                keyword: keyword,
                pageNum: this.pageNum,
                pageSize: this.pageSize
            }
        }).done(function(res) {
            self.setState(state => ({
                loading: false
            }));
            self.components.result.current.update(res);
            if (e.hasOwnProperty('callback')) {
                e.callback();
            }
            self.pageNum = 1;
        });
    }

    render() {
        const categories = this.props.data.categories.items.map((category) =>
            <li><input className="ml-1" name="search[category][]" type="checkbox" value={category.id}/><a className="ml-2" href="#" tabindex="-1">{category.title}</a></li>
        );
        const types = [{title: "Mien Phi"}, {title: "Tinh Phi"}].map((type) =>
            <li><input className="ml-1" name="search[price][]" type="checkbox" value="1"/><a className="ml-2" href="#" tabindex="-1">{type.title}</a></li>
        );

        return <div className="c-outsideBlock">
                    <div className="c-outsideBlock__contents mb-5">
                        <div className="row">
                            <div className="col-4">
                                <div className="position-relative">
                                    <button aria-expanded="false" className="btn btn-default dropdown-toggle form-control text-left" data-toggle="dropdown" style={{"border": "2px solid rgb(206, 212, 218)", "width": "100%", "border-radius": "0", "height": "40px"}}>
                                        <span>{t["customize.ownerstore.categories"]}</span>
                                        <span className="caret"></span>
                                    </button>
                                    <ul className="dropdown-menu w-100" x-placement="bottom-start" style={{"position": "absolute", "will-change": "transform", "top": "0px", "left": "0px", "transform": "translate3d(21px, 55px, 0px)"}}>
                                        {categories}
                                    </ul>
                                </div>
                            </div>
                            <div className="col-2 px-0">
                                <div className="position-relative">
                                    <button aria-expanded="false" className="btn btn-default dropdown-toggle form-control text-left" data-toggle="dropdown" style={{"border": "2px solid rgb(206, 212, 218)", "width": "100%", "border-radius": "0", "height": "40px"}}>
                                        <span>{t["admin.store.plugin.price"] }</span>
                                        <span className="caret"></span>
                                    </button>
                                    <ul className="dropdown-menu w-100" x-placement="bottom-start" style={{"position": "absolute", "will-change": "transform", "top": "0px", "left": "0px", "transform": "translate3d(21px, 55px, 0px)"}}>
                                        {types}
                                    </ul>
                                </div>
                            </div>
                            <div className="col-6">
                                <div className="d-flex">
                                    <input className="form-control border-right-0" name="search[keyword]" type="text" placeholder={t["common.search_keyword"]} style={{"border": "2px solid rgb(206, 212, 218)", "height": "40px", "border-radius": "0"}} />
                                    <button className="btn btn-ec-conversion px-5 ladda-button rounded-0" type="submit" data-style="expand-right" disabled={this.state.loading} onClick={this.submit}>
                                        <span className="ladda-label">{t["admin.store.plugin_owners_search.search_button"]}</span>
                                        {this.state.loading && <i className="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom ml-1" />}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>;
        }
}

class PaginationItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            loading:  false
        };
    }

    loading(flag) {
        this.setState((state) => ({
            loading: flag
        }));
    }

    submit(pageNum, e) {
        e.preventDefault();
        let self = this;
        self.loading(true);
        self.props.components.search.current.update({pageNum: pageNum});
        let event = new MouseEvent("click");
        event.callback = function(){self.loading(false);};
        self.props.components.search.current.submit(event);
    }
    render() {
        return <li className={"page-item" + (this.props.page.active && " active")}>
                <button className="page-link" disabled={this.props.page.disabled} onClick={this.submit.bind(this, this.props.page.number)}>
                    {!this.state.loading && <span>{this.props.page.number}</span>}
                    {this.state.loading && <i className="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom" />}
                </button>
            </li>;
    }
}

class Pagination extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            totals: props.totals || 0,
            pageSize: props.pageSize || 0,
            pageNum: props.pageNum || 0
        };;
        this.update = this.update.bind(this);
    }

    update(data) {
        if (data.hasOwnProperty("pageSize") && data.hasOwnProperty("pageNum") && data.hasOwnProperty("totals")) {
            this.setState((state) => ({
                totals: data.totals,
                pageSize: data.pageSize,
                pageNum: data.pageNum
            }));
        }
    }

    render() {
        let pages = [];
        let pageCount = Math.ceil(this.state.totals/this.state.pageSize);
        let pageNum = parseInt(this.state.pageNum);
        let availablePage = 2;
        let pageMax = pageNum + availablePage;
        let i = pageNum - availablePage;
        for (i;i<=pageMax;i++) {
            if (i > 0 && i <= pageCount) {
                pages.push({
                    number: i,
                    active: i === pageNum ? true : false,
                    disabled: false
                });
            }
        }
        const pageItems = pages.map((page) => <PaginationItem components={this.props.components} page={page} />);

        return <ul className="pagination justify-content-center">
            {pageItems}
            </ul>;
    }
}

class ResultItem extends React.Component {
    render() {
        return <div>
                <div className="row py-2">
                    <div className="col-4">
                        <div className="plugin-img">
                            <img alt="{this.props.item.title}" className="w-100 img-responsive" src="https://codecanyon.img.customer.envatousercontent.com/files/245053411/Small+Preview+590x300.jpg?auto=compress%2Cformat&fit=crop&crop=top&w=590&h=300&s=bad280e311cd41d3f93eca068e9a29c3" />
                        </div>
                    </div>
                    <div className="col-5">
                        <div className="plugin-title">
                            <h5 className="mb-0">
                                <a className="font-weight-bold" href="#" target="_blank">{this.props.item.title}</a>
                            </h5>
                        </div>
                        <div className="plugin-provider mb-3">
                            by <span><a href="#">OnShop</a></span> in <a href="#">Reporting</a>
                        </div>
                        <div className="plugin-version">
                            <label className="font-weight-bold plugin-attribute">Version:</label>
                            <span>1.0</span>
                            <span className="font-italic"><a href="#">View change log</a></span>
                        </div>
                        <div className="plugin-desc">
                            <p>{this.props.item.description}</p>
                        </div>
                    </div>
                    <div className="col-3 text-center border-left position-relative">
                        <div className="plugin-payment">
                            <div className="plugin-price text-danger d-flex justify-content-center align-items-end mb-2">
                                <h4 className="mb-0 font-weight-bold">{this.props.item.price}</h4>
                                <small className="pl-2">(có VAT)</small>
                            </div>
                            <div className="plugin-rating d-flex justify-content-center align-items-end h6 mb-0">
                                <p className="stars mb-0">
                                    <span className="star text-warning"><i className="fas fa-star"></i></span>
                                    <span className="star text-warning"><i className="fas fa-star"></i></span>
                                    <span className="star text-warning"><i className="fas fa-star"></i></span>
                                    <span className="star text-warning"><i className="fas fa-star-half-alt"></i></span>
                                    <span className="star text-light"><i className="fas fa-star"></i></span>
                                </p>
                                <small className="plugin-number text-muted pl-2">(1K)</small>
                            </div>
                            <div className="plugin-download font-italic text-muted">
                                <small>989 downloads</small>
                            </div>
                        </div>
                        <div className="plugin-action position-absolute fixed-bottom px-4 d-flex justify-content-center" style={{zIndex: 1}}>
                            <a className="btn btn-ec-regular w-50 mx-1" href="#">{t["admin.store.plugin_owners_search.detail"]}</a>
                            <a className="btn btn-primary w-50 mx-1" href="#">{t["admin.store.plugin_owners_search.install.free"]}</a>
                        </div>
                    </div>
                </div>
                <hr />
            </div>;
    }
}

class Result extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            items: props.data.items || []
        };
        this.paginationRef = React.createRef();

        this.update = this.update.bind(this);
    }

    update(data) {
        if (data.hasOwnProperty("items")) {
            this.setState(state => ({
                items: data.items
            }));
        }
        this.paginationRef.current.update(data);
    }

    render() {
        const resultItems = this.state.items.map((item) => <ResultItem item={item} />);
        let searchInfo = t["admin.store.plugin_owners_search.search_results"];
        if (this.props.data.hasOwnProperty("pageSize")) {
            searchInfo = searchInfo.replace("_pageSize_", this.props.data.pageSize);
        }
        if (this.props.data.hasOwnProperty("totals")) {
            searchInfo = searchInfo.replace("_totals_", this.props.data.totals);
        }

        return <div className="c-contentsArea__cols">
                    <div className="c-contentsArea__primaryCol">
                        <div className="c-primaryCol">
                            <div className="card rounded border-0 my-4">
                                <div className="card-header"><span className="font-weight-bold ml-2">{searchInfo}</span></div>
                                <div className="card-body">
                                    {resultItems}
                                </div>
                                <div className="card-footer">
                                    <Pagination ref={this.paginationRef} components={this.props.components} pageSize={this.props.data.pageSize} pageNum={this.props.data.pageNum} totals={this.props.data.totals} />
                                </div>
                            </div>
                        </div>
                    </div>
            </div>;
    }
}

class App extends React.Component {
    constructor(props) {
        super(props);
        this.resultRef = React.createRef();
        this.searchRef = React.createRef();
    }

    render() {
        return <div>
                <Search ref={this.searchRef} components={{result: this.resultRef}} data={this.props.data.search} />
                <Result ref={this.resultRef} components={{search: this.searchRef}} data={this.props.data.result}  />
            </div>;
    }
}