

Espo.define('crm:views/dashlets/calendar', 'views/dashlets/abstract/base', function (Dep) {

    return Dep.extend({

        name: 'Calendar',

        noPadding: true,

        _template: '<div class="calendar-container">{{{calendar}}} </div>',

        init: function () {
            Dep.prototype.init.call(this);
        },

        afterRender: function () {
            var mode = this.getOption('mode');
            if (mode === 'timeline') {
                var userList = [];
                var userIdList = this.getOption('usersIds') || [];
                var userNames = this.getOption('usersNames') || {};
                userIdList.forEach(function (id) {
                    userList.push({
                        id: id,
                        name: userNames[id] || id
                    });
                }, this);

                var viewName = this.getMetadata().get(['clientDefs', 'Calendar', 'timelineView']) || 'crm:views/calendar/timeline';

                this.createView('calendar', viewName, {
                    el: this.options.el + ' > .calendar-container',
                    header: false,
                    calendarType: 'shared',
                    userList: userList,
                    enabledScopeList: this.getOption('enabledScopeList'),
                    noFetchLoadingMessage: true,
                }, function (view) {
                    view.render();
                }, this);
            } else {
                var teamIdList = null;

                if (~['basicWeek', 'month', 'basicDay'].indexOf(mode)) {
                    teamIdList = this.getOption('teamsIds');
                }

                var viewName = this.getMetadata().get(['clientDefs', 'Calendar', 'calendarView']) || 'crm:views/calendar/calendar';

                this.createView('calendar', viewName, {
                    mode: mode,
                    el: this.options.el + ' > .calendar-container',
                    header: false,
                    enabledScopeList: this.getOption('enabledScopeList'),
                    containerSelector: this.options.el,
                    teamIdList: teamIdList
                }, function (view) {
                    this.listenTo(view, 'view', function () {
                        if (this.getOption('mode') === 'month') {
                            var title = this.getOption('title');
                            var $headerSpan = this.$el.closest('.panel').find('.panel-heading > .panel-title > span');
                            title += ' &raquo; ' + view.getTitle();
                            $headerSpan.html(title);
                        }
                    }, this);
                    view.render();

                    this.on('resize', function () {
                        setTimeout(function() {
                            view.adjustSize();
                        }, 50);
                    });
                }, this);
            }
        },

        setupActionList: function () {
            this.actionList.unshift({
                name: 'viewCalendar',
                html: this.translate('View Calendar', 'labels', 'Calendar'),
                url: '#Calendar',
                iconHtml: '<span class="far fa-calendar-alt"></span>'
            });
        },

        setupButtonList: function () {
            if (this.getOption('mode') !== 'timeline') {
                this.buttonList.push({
                    name: 'previous',
                    html: '<span class="fas fa-chevron-left"></span>',
                });
                this.buttonList.push({
                    name: 'next',
                    html: '<span class="fas fa-chevron-right"></span>',
                });
            }
        },

        actionRefresh: function () {
            var view = this.getView('calendar');
            if (!view) return;
            view.actionRefresh();
        },

        actionNext: function () {
            var view = this.getView('calendar');
            if (!view) return;
            view.actionNext();
        },

        actionPrevious: function () {
            var view = this.getView('calendar');
            if (!view) return;
            view.actionPrevious();
        },

        actionViewCalendar: function () {
            this.getRouter().navigate('#Calendar', {trigger: true});
        }
    });
});


