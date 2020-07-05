

Espo.define('views/modals/last-viewed', ['views/modal', 'search-manager'], function (Dep, SearchManager) {

    return Dep.extend({

        header: false,

        scope: 'ActionHistoryRecord',

        className: 'dialog dialog-record',

        template: 'modals/last-viewed',

        backdrop: true,

        events: _.extend({
            'click .list .cell > a': function () {
                this.close();
            },
        }, Dep.prototype.events),

        setup: function () {
            Dep.prototype.setup.call(this);

            this.buttonList = [
                {
                    name: 'cancel',
                    label: 'Close'
                }
            ];

            this.headerHtml = this.getLanguage().translate('LastViewed', 'scopeNamesPlural');
            this.headerHtml = '<a href="#LastViewed" class="action" data-action="listView">' + this.headerHtml + '</a>';

            this.waitForView('list');

            this.getCollectionFactory().create(this.scope, function (collection) {
                collection.maxSize = this.getConfig().get('recordsPerPage');
                this.collection = collection;

                collection.url = 'LastViewed';

                this.loadList();
                collection.fetch();
            }, this);

        },

        actionListView: function () {
            this.getRouter().navigate('#LastViewed', {trigger: true});
            this.close();
        },

        loadList: function () {
            var viewName = this.getMetadata().get('clientDefs.' + this.scope + '.recordViews.listLastViewed') ||
                           'views/record/list';

            this.listenToOnce(this.collection, 'sync', function () {
                this.createView('list', viewName, {
                    collection: this.collection,
                    el: this.containerSelector + ' .list-container',
                    selectable: false,
                    checkboxes: false,
                    massActionsDisabled: true,
                    rowActionsView: false,
                    type: 'listLastViewed',
                    searchManager: this.searchManager,
                    checkAllResultDisabled: true,
                    buttonsDisabled: true,
                    headerDisabled: true
                });
            }, this);
        },
    });
});
