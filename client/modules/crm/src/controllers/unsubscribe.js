

Espo.define('crm:controllers/unsubscribe', 'controller', function (Dep) {

    return Dep.extend({

        actionUnsubscribe: function (data) {
            var viewName = this.getMetadata().get(['clientDefs', 'Campaign', 'unsubscribeView']) ||
                'crm:views/campaign/unsubscribe';

            this.entire(viewName, {
                actionData: data
            }, function (view) {
                view.render();
            });
        },

        actionSubscribeAgain: function (data) {
            var viewName = this.getMetadata().get(['clientDefs', 'Campaign', 'subscribeAgainView']) ||
                'crm:views/campaign/subscribe-again';

            this.entire(viewName, {
                actionData: data
            }, function (view) {
                view.render();
            });
        }

    });
});
