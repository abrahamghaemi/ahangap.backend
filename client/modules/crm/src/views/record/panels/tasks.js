

Espo.define('crm:views/record/panels/tasks', 'views/record/panels/relationship', function (Dep) {

    return Dep.extend({

        name: 'tasks',

        scope: 'Task',

        filterList: ['all', 'actual', 'completed'],

        defaultTab: 'actual',

        orderBy: 'createdAt',

        orderDirection: 'desc',

        rowActionsView: 'crm:views/record/row-actions/tasks',

        buttonList: [
            {
                action: 'createTask',
                title: 'Create Task',
                acl: 'create',
                aclScope: 'Task',
                html: '<span class="fas fa-plus"></span>',
            }
        ],

        actionList: [
            {
                label: 'View List',
                action: 'viewRelatedList'
            }
        ],

        listLayout: {
            rows: [
                [
                    {
                        name: 'name',
                        link: true,
                    },
                    {
                        name: 'isOverdue'
                    }
                ],
                [
                    {name: 'assignedUser'},
                    {name: 'status'},
                    {name: 'dateEnd'},
                ]
            ]
        },

        setup: function () {
            this.parentScope = this.model.name;
            this.link = 'tasks';

            this.panelName = 'tasksSide';

            this.defs.create = true;

            if (this.parentScope == 'Account') {
                this.link = 'tasksPrimary';
            }

            this.url = this.model.name + '/' + this.model.id + '/' + this.link;

            this.setupSorting();

            if (this.filterList && this.filterList.length) {
                this.filter = this.getStoredFilter();
            }

            this.setupFilterActions();

            this.setupTitle();

            this.wait(true);

            this.getCollectionFactory().create('Task', function (collection) {
                this.collection = collection;
                collection.seeds = this.seeds;
                collection.url = this.url;
                collection.orderBy = this.defaultOrderBy;
                collection.order = this.defaultOrder;
                collection.maxSize = this.getConfig().get('recordsPerPageSmall') || 5;

                this.setFilter(this.filter);

                this.wait(false);
            }, this);
        },

        afterRender: function () {
            this.createView('list', 'views/record/list-expanded', {
                el: this.getSelector() + ' > .list-container',
                pagination: false,
                type: 'listRelationship',
                rowActionsView: this.defs.rowActionsView || this.rowActionsView,
                checkboxes: false,
                collection: this.collection,
                listLayout: this.listLayout,
                skipBuildRows: true
            }, function (view) {
                view.getSelectAttributeList(function (selectAttributeList) {
                    if (selectAttributeList) {
                        this.collection.data.select = selectAttributeList.join(',');
                    }

                    if (!this.disabled) {
                        this.collection.fetch();
                    } else {
                        this.once('show', function () {
                            this.collection.fetch();
                        }, this);
                    }
                }.bind(this));
            });
        },

        actionCreateRelated: function () {
            this.actionCreateTask();
        },

        actionCreateTask: function (data) {
            var self = this;
            var link = this.link;
            if (this.parentScope === 'Account') {
                link = 'tasks';
            }
            var scope = 'Task';
            var foreignLink = this.model.defs['links'][link].foreign;

            this.notify('Loading...');

            var viewName = this.getMetadata().get('clientDefs.' + scope + '.modalViews.edit') || 'views/modals/edit';

            this.createView('quickCreate', viewName, {
                scope: scope,
                relate: {
                    model: this.model,
                    link: foreignLink,
                }
            }, function (view) {
                view.render();
                view.notify(false);
                this.listenToOnce(view, 'after:save', function () {
                    this.collection.fetch();
                    this.model.trigger('after:relate');
                }, this);
            });

        },

        actionRefresh: function () {
            this.collection.fetch();
        },

        actionComplete: function (data) {
            var id = data.id;
            if (!id) {
                return;
            }
            var model = this.collection.get(id);
            model.save({
                status: 'Completed'
            }, {
                patch: true,
                success: function () {
                    this.collection.fetch();
                }.bind(this)
            });
        },

        actionViewRelatedList: function (data) {
            data.viewOptions = data.viewOptions || {};
            data.viewOptions.massUnlinkDisabled = true;

            Dep.prototype.actionViewRelatedList.call(this, data);
        }

    });
});
