

Espo.define('views/user/record/detail-side', 'views/record/detail-side', function (Dep) {

    return Dep.extend({

        setupPanels: function () {
            Dep.prototype.setupPanels.call(this);

            if (this.model.isApi() || this.model.isSystem()) {
                this.hidePanel('activities');
                this.hidePanel('history');
                this.hidePanel('tasks');
                this.hidePanel('stream');
                return;
            }

            var showActivities = this.getAcl().checkUserPermission(this.model);

            if (!showActivities) {
                if (this.getAcl().get('userPermission') === 'team') {
                    if (!this.model.has('teamsIds')) {
                        this.listenToOnce(this.model, 'sync', function () {
                            if (this.getAcl().checkUserPermission(this.model)) {
                                this.showPanel('activities', function () {
                                    this.getView('activities').actionRefresh();
                                });
                                this.showPanel('history', function () {
                                    this.getView('history').actionRefresh();
                                });
                                if (!this.model.isPortal()) {
                                    this.showPanel('tasks', function () {
                                        this.getView('tasks').actionRefresh();
                                    });
                                }
                            }
                        }, this);
                    }
                }
            }

            if (!showActivities) {
                this.hidePanel('activities');
                this.hidePanel('history');
                this.hidePanel('tasks');
            }

            if (this.model.isPortal()) {
                this.hidePanel('tasks');
            }
        }
    });
});
