let $ = jQuery.noConflict();

$(function () {
    $("article").find("[original_id]").prepend("<button data-bs-toggle=\"modal\" data-bs-target=\"#translation_doc\" title=\"点击翻译\" class=\"customize-partial-edit-shortcut-button\"><svg xmlns=\"http://www.w3.org/2000/svg\" viewBox=\"0 0 20 20\"><path d=\"M13.89 3.39l2.71 2.72c.46.46.42 1.24.03 1.64l-8.01 8.02-5.56 1.16 1.16-5.58s7.6-7.63 7.99-8.03c.39-.39 1.22-.39 1.68.07zm-2.73 2.79l-5.59 5.61 1.11 1.11 5.54-5.65zm-2.97 8.23l5.58-5.6-1.07-1.08-5.59 5.6z\"></path></svg></button>")

    $(".customize-partial-edit-shortcut-button").click(function () {
        const formData = new FormData();
        const id = $(this).parent().attr("original_id");
        formData.append("project", translate_project_path);
        formData.append("translation_set_slug", "default");
        formData.append("locale_slug", "zh-cn");
        formData.append("original_ids", JSON.stringify({"original_ids": id}));
        $.ajax({
            url: "https://translate.wenpai.org/api/translations/-query-by-originals/",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            success: function (s) {
                console.log(s);

                $(".original").text(s[0].original).attr("original_id", s[0].original_id);
                if (s[0].translations.length != 0) {
                    if (s[0].translations[0].status === "waitting") {
                        $("#translate-status").text("状态：正在审核");
                    } else if (s[0].translations[0].status === "fuzzy") {
                        $("#translate-status").text("状态：模糊");
                    } else if (s[0].translations[0].status === "current") {
                        $("#translate-status").text("状态：当前");
                    } else if (s[0].translations[0].status === "old") {
                        $("#translate-status").text("状态：旧");
                    }

                    if (s[0].translations[0].translation_0 != null) {
                        $(".original_zh-cn").text(s[0].translations[0].translation_0);
                        $(".translation-text").val($(".original_zh-cn").text())
                    }
                }

                $(".copy-original").click(function () {
                    $(".translation-text").val($(".original").text())
                });
            },
            error: function (e) {
                console.log(e);
            }
        });

    });


    $("#submit-translate").click(function () {
        const formData = new FormData();
        const original_id = $(".original").attr("original_id");
        const translation_text = $(".translation-text").val()

        let translation = {
            [original_id]:
                [
                    translation_text
                ]
        }

        formData.append("project", translate_project_path);
        formData.append("locale_slug", "zh-cn");
        formData.append("translation", JSON.stringify(translation));

        $.ajax({
            url: "https://translate.wenpai.org/api/translations/-new/",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            xhrFields: {
                withCredentials: true
            },
            crossDomain: true,
            beforeSend: function (b) {

            },
            success: function (s) {
                console.log(s);
                if (s[original_id].translation_status === "current") {
                    alert("提交翻译成功,2秒后刷新页面");
                    setTimeout(function () {
                        window.location.reload();// 刷新当前页面.
                    }, 2000)
                } else if (s[original_id].translation_status === "waitting") {
                    alert("提交成功，请等待管理员审核");
                }

            },
            error: function (e) {
                if (e.status === 409) {
                    alert("翻译已存在，请勿重复提交");
                }
            }
        });

    });
});
