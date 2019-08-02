class Search extends React.Component {
    constructor(props) {
        super(props);
        this.pageNum = props.params.pageNum || 0;
        this.pageSize = props.params.pageSize || 0;
        this.state = {
            loading: false,
            keyword: ""
        };
        this.submit = this.submit.bind(this);
        this.loading = this.loading.bind(this);
    }

    loading(flag) {
        let self = this;
        self.setState(state => ({
            loading: flag
        }));
    }

    update (data) {
        if (data.hasOwnProperty("pageNum")) {
            this.pageNum = data.pageNum;
        }
    }

    submit (e) {
        e.preventDefault();
        let self = this;
        self.loading(true);
        let categories = $.map($('input[name="search[category][]"]:checked'), function(c){return c.value; });
        let keyword = $('input[name="search[keyword]"]').val();
        let price = $.map($('input[name="search[price][]"]:checked'), function(c){return c.value; })
        $.ajax({
            type: "POST",
            url: self.props.params.submitUrl,
            data: {
                categories: categories,
                keyword: keyword,
                price: price,
                pageNum: this.pageNum,
                pageSize: this.pageSize
            }
        }).done(function(res) {
            self.loading(false);
            self.props.components.result.current.update(res);
            if (e.hasOwnProperty('callback')) {
                e.callback();
            }
            self.pageNum = 1;
        });
    }

    render() {
        const categories = this.props.params.categories.items.map((category) =>
            <li><input className="ml-1" name="search[category][]" type="checkbox" value={category.id}/><a className="ml-2" href="#" tabindex="-1">{category.title}</a></li>
        );
        const types = [{value: "free", title: t["customize.store.price.free"]}, {value:"paid", title: t["customize.store.price.paid"]}].map((type) =>
            <li><input className="ml-1" name="search[price][]" type="checkbox" value={type.value}/><a className="ml-2" href="#" tabindex="-1">{type.title}</a></li>
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
        this.loading = this.loading.bind(this);
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
                    disabled: i === pageNum ? true : false
                });
            }
        }
        const pageItems = pages.map((page) => <PaginationItem components={this.props.components} page={page} />);

        return <ul className="pagination justify-content-center">{pageItems}</ul>;
    }
}

class ResultItem extends React.Component {
    constructor(props) {
        super(props);
        this.state = {
            status: "install"
        };
        this.install = this.install.bind(this);
    }
    install(e) {
        e.preventDefault();
        let self = this;
        self.props.components.modal.current.update({
            title: t["customize.store.confirm"],
            body: t["customize.store.plugin.confirm_install"],
            callback: function () {
                $.ajax({
                    type: "POST",
                    url: self.props.params.installUrl,
                    data: {
                        packageUrl: self.props.item.package.url || "",
                        name: self.props.item.title || "",
                        code: self.props.item.package.code || ""
                    },
                    beforeSend: function () {
                        self.setState(state => ({
                            status: "loading"
                        }));
                    }
                }).done(function(res) {
                    if (res.hasOwnProperty("result") && res.result == true) {
                        self.setState(state => ({
                            status: "installed"
                        }));
                        let message = {
                            id: Date.now(),
                            className: "alert-success",
                            message: t["customize.store.message.install_success"].replace("_link_", '<a class="alert-link" href="'+ r["admin_store_plugin"] +'">' + r["admin_store_plugin"] + '</a>')
                        };
                        self.props.components.alert.current.insertLast(message);
                        setTimeout(function () {
                            $('#' + message.id).alert('close');
                        }, 3000);
                    } else {
                        self.setState(state => ({
                            status: "install"
                        }));
                        let message = {
                            id: Date.now(),
                            className: "alert-danger",
                            message: t["customize.store.message.system_error"]
                        };
                        self.props.components.alert.current.insertLast(message);
                        setTimeout(function () {
                            $('#' + message.id).alert('close');
                        }, 3000);
                    }
                });
            }
        });
        self.props.components.modal.current.show();
    }

    render() {
        return <div>
                    <div className="row py-2">
                        <div className="col-4">
                            <div className="plugin-img">
                                <img alt={this.props.item.title} className="w-100 img-responsive" src={this.props.item.thumb} />
                            </div>
                        </div>
                    <div className="col-5">
                        <div className="plugin-title">
                            <h5 className="mb-0">
                                <a className="font-weight-bold" href="#" target="_blank">{this.props.item.title}</a>
                            </h5>
                        </div>
                        <div className="plugin-provider mb-3">
                            by <span><a href="#">{this.props.item.provider}</a></span> in <a href="#">{this.props.item.category.title}</a>
                        </div>
                        <div className="plugin-version">
                            <label className="font-weight-bold plugin-attribute">{t["customize.store.version"]}:&nbsp;</label>
                            <span>{this.props.item.package.version}</span>
                            <span className="font-italic"><a href="#">View change log</a></span>
                        </div>
                        <div className="plugin-desc"><p>{this.props.item.description}</p></div>
                    </div>
                    <div className="col-3 text-center border-left position-relative">
                        <div className="plugin-payment">
                            <div className="plugin-price text-danger d-flex justify-content-center align-items-end mb-2">
                                <h4 className="mb-0 font-weight-bold">{parseInt(this.props.item.price) > 0 ? this.props.item.price : t["customize.store.price.free"]}</h4>
                                {parseInt(this.props.item.price) > 0 && <small className="pl-2">{t["common.tax_include"]}</small>}
                            </div>
                        <div className="plugin-rating d-flex justify-content-center align-items-end h6 mb-0">
                            <p className="stars mb-0">
                                <span className={"star" + (this.props.item.rate > 0 ? " text-warning" : " text-light")}><i className={"fas fa-star" + (this.props.item.rate>0 && this.props.item.rate<1  ? " fa-star-half-alt" : "")}></i></span>
                                <span className={"star" + (this.props.item.rate > 1 ? " text-warning" : " text-light")}><i className={"fas fa-star" + (this.props.item.rate>1 && this.props.item.rate<2  ? " fa-star-half-alt" : "")}></i></span>
                                <span className={"star" + (this.props.item.rate > 2 ? " text-warning" : " text-light")}><i className={"fas fa-star" + (this.props.item.rate>2 && this.props.item.rate<3  ? " fa-star-half-alt" : "")}></i></span>
                                <span className={"star" + (this.props.item.rate > 3 ? " text-warning" : " text-light")}><i className={"fas fa-star" + (this.props.item.rate>3 && this.props.item.rate<4  ? " fa-star-half-alt" : "")}></i></span>
                                <span className={"star" + (this.props.item.rate > 4 ? " text-warning" : " text-light")}><i className={"fas fa-star" + (this.props.item.rate>4 && this.props.item.rate<5  ? " fa-star-half-alt" : "")}></i></span>
                            </p>
                            {/*<small className="plugin-number text-muted pl-2">(1K)</small>*/}
                        </div>
                        <div className="plugin-download font-italic text-muted"><small>{this.props.item.downloadCount} {t["customize.store.downloadCount"]}</small></div>
                    </div>
                    <div className="plugin-action position-absolute fixed-bottom px-4 d-flex justify-content-center" style={{zIndex: 1}}>
                        <a className="btn btn-ec-regular w-50 mx-1" href="#">{t["admin.store.plugin_owners_search.detail"]}</a>
                        { this.state.status == "install" &&
                            <button className="btn btn-primary w-50 mx-1" onClick={this.install}>{t["admin.store.plugin_owners_search.install.free"]}</button>
                        }
                        { this.state.status == "installed" &&
                            <button className="btn btn-success w-50 mx-1" readonly>{t["customize.store.uptodated"]}</button>
                        }
                        { this.state.status == "loading" &&
                            <button className="btn btn-primary w-50 mx-1" disabled><span>{t["customize.store.processing"]}&nbsp;<i className="fa fa-spinner fa-pulse fa-1x fa-fw margin-bottom" /></span></button>
                        }
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
            items: props.params.pagination.items || []
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
        const resultItemParams = {
            installUrl: this.props.params.installUrl
        };
        const resultItems = this.state.items.map((item) => <ResultItem params={resultItemParams} components={this.props.components} item={item} />);
        let searchInfo = t["admin.store.plugin_owners_search.search_results"];
        if (this.props.params.pagination.hasOwnProperty("pageNum")) {
            searchInfo = searchInfo.replace("_pageSize_", this.props.params.pagination.pageNum);
        }
        if (this.props.params.pagination.hasOwnProperty("totals") && this.props.params.pagination.hasOwnProperty("pageSize")) {
            let pageCount = parseInt(this.props.params.pagination.totals) / parseInt(this.props.params.pagination.pageSize);
            pageCount = Math.ceil(pageCount);
            searchInfo = searchInfo.replace("_totals_", pageCount);
        }
        return <div className="c-contentsArea__cols">
                    <div className="c-contentsArea__primaryCol">
                        <div className="c-primaryCol">
                            <div className="card rounded border-0 my-4">
                                <div className="card-header"><span className="font-weight-bold ml-2">{searchInfo}</span></div>
                                <div className="card-body">
                                    {resultItems}
                                    {this.state.items.length === 0 &&
                                        <div className="alert alert-warning" role="alert">
                                            <i className="fa fa-search fa-lg mr-2"></i>
                                            <span className="font-weight-bold">{t["customize.store.message.not_found"]}</span>
                                        </div>
                                    }
                                </div>
                                <div className="card-footer">
                                    <Pagination ref={this.paginationRef} components={this.props.components} pageSize={this.props.params.pagination.pageSize} pageNum={this.props.params.pagination.pageNum} totals={this.props.params.pagination.totals} />
                                </div>
                            </div>
                        </div>
                    </div>
        </div>;
    }
}

class Modal extends React.Component {
    constructor(props) {
        super(props);
        this.callback = null;
        this.state = {
            title: props.title || "",
            body: props.body || "",
        };
        this.submit = this.submit.bind(this);
    }
    update(data) {
        this.setState((state) => ({
            title: data.title,
            body: data.body
        }));
        if (data.hasOwnProperty("callback")) {
            this.callback = data.callback;
        }
    }
    show() {
        $("#modal").modal("show");
    }
    hide() {
        $("#modal").modal("hide");
    }
    submit(e) {
        e.preventDefault();
        let self = this;
        self.hide();
        if (typeof self.callback == "function") {
            self.callback();
        }
    }
    render() {
        return <div id="modal" className="modal" tabindex="-1" role="dialog">
                    <div className="modal-dialog" role="document">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">{this.state.title}</h5>
                            </div>
                            <div className="modal-body">{this.state.body}</div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-primary" onClick={this.submit}>Đồng ý</button>
                                <button type="button" className="btn btn-secondary" data-dismiss="modal">Bỏ qua</button>
                            </div>
                    </div>
                </div>
        </div>;
    }
}

class Alert extends React.Component
{
    constructor (props) {
        super(props);
        this.state = {
            messages: props.messages || []
        };
        this.insertLast.bind(this);
        this.removeFirst.bind(this);
    }

    insertLast(message) {
        let messages = this.state.messages.slice(0);
        messages.push(message);
        this.setState((state) => ({
            messages: messages
        }));

    }

    removeFirst() {
        let messages = this.state.messages.slice(0);
        messages.shift();
        this.setState((state) => ({
            messages: messages
        }));
    }

    render() {
        const alerts = this.state.messages.map((alert) =>
            <div id={alert.id} className={"mb-0 alert alert-dismissible fade show " + alert.className} role="alert">
                <span dangerouslySetInnerHTML={{__html: alert.message}}></span>
                <button type="button" className="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        );
        return <div style={{position:"fixed",bottom:0,zIndex:9,width:"100%",margin:0, left:"220px"}}>{alerts}</div>;
    }
}

class Listing extends React.Component {
    constructor(props) {
        super(props);
        this.resultRef = React.createRef();
        this.searchRef = React.createRef();
        this.modalRef = React.createRef();
        this.alertRef = React.createRef();
    }

    render() {
        return <div>
            <Alert ref={this.alertRef} />
            <Modal ref={this.modalRef} />
            <Search ref={this.searchRef} components={{result: this.resultRef}} params={this.props.params.search} />
            <Result ref={this.resultRef} components={{search: this.searchRef, modal: this.modalRef, alert: this.alertRef}} params={this.props.params.result}  />
        </div>;
    }
}