jQuery(document).ready(function ($) {
    $(".auto-translate").click(async function () {
        const original = $(this).closest(".panel-content").find(".original_raw").text();
        const textarea = $(this).closest(".textareas.active").find("textarea");
        textarea.val("翻译中，请稍等片刻...");

        try {
            const request = await fetch('/cloud-translate/', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    originals: [original],
                })
            })

            if (!request.ok) {
                textarea.val('接口异常，请联系管理员处理。');
                return;
            }

            let data = await request.json();

            if (data.error !== undefined) {
                textarea.val(data.error);
                return;
            }

            textarea.val(data[original]);
        } catch (err) {
            textarea.val('接口异常，请联系管理员处理。');
        }
    })
})
