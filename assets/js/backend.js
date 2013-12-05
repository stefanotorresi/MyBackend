Backend = function(config)
{
    Application.call(this, config);
};

Backend.prototype = $.extend(Application.prototype, {constructor: Backend});
