$(function () {
    $("#status").bootstrapToggle({
        on: "Active",
        off: "InActive",
    });
});

ClassicEditor.create(document.querySelector("#answer")).catch((error) => {
    console.error(error);
});
