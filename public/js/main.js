$(document).ready(function () {
	$(document).on("click", "#addPhotoBtn", function (e) {
		const html = $(".div-invi").html();
		$(".div-user-photos").append(html);
	});

	$(document).on("click", "#downloadPdfBtn", function (e) {
		const filepath = $(this).data("filepath");

		window.open(filepath);
	});

	$(document).on("click", "#liGenerator", function (e) {
		window.location = "/pdf";
	});

	$(document).on("click", "#liPdfList", function (e) {
		window.location = "/pdf/list";
	});
});