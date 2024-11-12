const originalValues = {
  "prod-image": document.getElementById("prod-image").src,
  "prod-name": document.getElementById("prod-name").value,
  "prod-desc": document.getElementById("prod-desc").value,
  "prod-unit": document.getElementById("prod-unit").value,
};

document.getElementById("edit-btn").addEventListener("click", function () {
  const imgLabel = document.querySelector(".image-container label");
  const disabledElems = document.querySelectorAll("*[disabled]");
  const actionBtns = document.querySelector(".action-btns");

  disabledElems.forEach((element) => {
    // Keep ID readonly
    if (element.id !== "prod-id") {
      element.disabled = false;
    }
  });

  imgLabel.style.display = "flex";
  actionBtns.style.display = "flex";
  this.style.display = "none";
});

document.getElementById("image-input").addEventListener("change", function () {
  const imgPreview = document.getElementById("prod-image");
  imgPreview.src = URL.createObjectURL(this.files[0]);
});

document
  .querySelector(".details-container .reset-btn")
  .addEventListener("click", function () {
    document.getElementById("prod-image").src = originalValues["prod-image"];

    for (const key in originalValues) {
      if (key !== "prod-image") {
        const elem = document.getElementById(key);
        elem.value = originalValues[key];
      }
    }
  });
