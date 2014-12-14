
(function() {

    var App = {};

    App.initialize = function() {
        var self = this;

        $(function() {
            self._initializeDom();
        });
    };

    App._initializeDom = function() {
        var self = this;

        $("[data-panel-group]").each(function() {
            new self.PanelGroup(this);
        });
    };

    App.PanelGroup = function(target) {
        this._target = $(target);
        this._setup();
    };

    App.PanelGroup.prototype._setup = function() {
        var self = this;

        this._target.find("[data-panel]").hide();

        this._target.find("[rel], [data-rel]").click(function(e) {
            e.preventDefault();
            var bt = $(this);
            self.select(bt.attr("rel") || bt.attr("data-rel"));
        });

        this.select(this._target.find("[data-panel]:first"));
    };

    App.PanelGroup.prototype.select = function(panelName) {
        if (panelName instanceof jQuery) {
            panelName = panelName.attr("data-panel") ||
                panelName.attr("rel") ||
                panelName.attr("data-rel");
        }

        this._target
            .find("[data-panel]").hide()
            .filter("[data-panel='" + panelName + "']").show();

        this._target.find("[rel], [data-rel]").removeClass("active");
        this._target.find("[rel='" + panelName + "'], [data-rel='" +
            panelName + "']").addClass("active");
    };

    App.initialize();

})();
