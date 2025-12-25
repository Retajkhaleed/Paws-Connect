function showForm(type) {

    const forms = ["adopt", "lost", "sick"];

    forms.forEach(t => {
        document.getElementById("form-" + t).classList.remove("active");
        document.getElementById("card-" + t).classList.remove("active");
    });

    document.getElementById("form-" + type).classList.add("active");
    document.getElementById("card-" + type).classList.add("active");
}

window.onload = function () {
    showForm("adopt");
};

