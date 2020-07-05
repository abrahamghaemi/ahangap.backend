

define('web-socket-manager', [], function () {

    var WebSocketManager = function (config) {
        this.config = config;
        var url = this.config.get('webSocketUrl');

        if (url) {
            if (url.indexOf('wss://') === 0) {
                this.url = url.substr(6);
                this.protocolPart = 'wss://';
            } else {
                this.url = url.substr(5);
                this.protocolPart = 'ws://';
            }
        } else {
            var siteUrl = this.config.get('siteUrl') || '';
            if (siteUrl.indexOf('https://') === 0) {
                this.url = siteUrl.substr(8);
                this.protocolPart = 'wss://';
            } else {
                this.url = siteUrl.substr(7);
                this.protocolPart = 'ws://';
            }


            if (~this.url.indexOf('/')) {
                this.url = this.url.replace(/\/$/, '');
            }

            if (this.protocolPart === 'wss://') {
                var port = 443;
            } else {
                var port = 8080;
            }

            var si = this.url.indexOf('/');
            if (~si) {
                this.url = this.url.substr(0, si) + ':' + port;
            } else {
                this.url += ':' + port;
            }

            if (this.protocolPart == 'wss://') {
                this.url += '/wss';
            }
        }

        this.subscribeQueue = [];
    };

    _.extend(WebSocketManager.prototype, {

        connect: function (auth, userId) {
            try {
                var authArray = Base64.decode(auth).split(':');
                var username = authArray[0];
                var authToken = authArray[1];
                var url = this.protocolPart + this.url;

                url += '?authToken=' + authToken + '&userId=' + userId;

                var connection = this.connection = new ab.Session(url,
                    function () {
                        this.isConnected = true;
                        this.subscribeQueue.forEach(function (item) {
                            this.subscribe(item.category, item.callback);
                        }, this);
                        this.subscribeQueue = [];
                    }.bind(this),
                    function () {},
                    {'skipSubprotocolCheck': true}
                );
            } catch (e) {
                console.error(e.message);
                this.connection = null;
            }
        },

        subscribe: function (category, callback) {
            if (!this.connection) return;
            if (!this.isConnected) {
                this.subscribeQueue.push({category: category, callback: callback});
                return;
            }
            try {
                this.connection.subscribe(category, callback);
            } catch (e) {
                if (e.message) {
                    console.error(e.message);
                } else {
                    console.error("WebSocket: Coud not subscribe to "+category+".");
                }
            }
        },

        unsubscribe: function (category, callback) {
            if (!this.connection) return;
            try {
                this.connection.unsubscribe(category, callback);
            } catch (e) {
                if (e.message) {
                    console.error(e.message);
                } else {
                    console.error("WebSocket: Coud not unsubscribe from "+category+".");
                }
            }
        },

        close: function () {
            if (!this.connection) return;
            try {
                this.connection.close();
            } catch (e) {
                console.error(e.message);
            }

            this.isConnected = false;
        },
    });

    return WebSocketManager;
});
