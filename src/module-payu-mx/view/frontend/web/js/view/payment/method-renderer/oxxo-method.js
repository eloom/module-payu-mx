define(["Eloom_Payment/js/cash"],function(a){return a.extend({defaults:{template:"Eloom_PayUMx/payment/oxxo-form",code:"eloom_payments_payu_oxxo"},initialize:function(){this._super()},isActive:function(){return!0},getLogoUrl:function(){return window.checkoutConfig.payment.eloom_payments_payu.url.logo}})});
