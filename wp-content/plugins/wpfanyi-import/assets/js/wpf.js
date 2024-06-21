(function ($) {
  $(".wpfanyi-tab-title li").click(function () {
    var TabNum = $(this).index();
    sessionStorage.setItem("TabNum", TabNum);
    var getTabNum = sessionStorage.getItem("TabNum");
    $(".wpfanyi-tab-title li").eq(getTabNum).addClass("wpfanyi-this").siblings().removeClass("wpfanyi-this");
    $(".wpfanyi-tab-content>div").eq(getTabNum).addClass("wpfanyi-show").siblings().removeClass("wpfanyi-show");
  });
  $(function () {
    var getTabNum = sessionStorage.getItem("TabNum");
    $(".wpfanyi-tab-title li").eq(getTabNum).addClass("wpfanyi-this").siblings().removeClass("wpfanyi-this");
    $(".wpfanyi-tab-content>div").eq(getTabNum).addClass("wpfanyi-show").siblings().removeClass("wpfanyi-show");
  })

  $(".wpfanyi-import-config-help .handle").click(function () {
    $(".inside").slideToggle();
  });

  $("form").sisyphus();
  $("input").focus(function () {
    $(this).parent().children(".input_clear").show();
  });
  $("input").blur(function () {
    if ($(this).val() == '') {
      $(this).parent().children(".input_clear").hide();
    }
  });
  $(".input_clear").click(function () {
    $(this).parent().find('input').val('');
    $(this).hide();
  });
})(jQuery);
