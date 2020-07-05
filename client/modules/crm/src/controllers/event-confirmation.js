

define('crm:controllers/event-confirmation', 'controller', function (Dep) {

    return Dep.extend({

        actionConfirmEvent: function (actionData) {
            var viewName = this.getMetadata().get(['clientDefs', 'EventConfirmation', 'confirmationView']) ||
                'crm:views/event-confirmation/confirmation';
            this.entire(viewName, {
                actionData: actionData
            }, function (view) {
                view.render();
            });
        }

    });
});
