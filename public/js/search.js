function clearResults(id) {
  if (document.querySelector(`#${id}-results`)) {
    document.querySelector(`#${id}-results`).remove();
  }
}

function handleKeydown(id, e) {
  switch (e.key) {
    case "Escape":
      clearResults(id);
      break;
    case "ArrowDown":
      handleArrowDown(id);
      break;
    case "ArrowUp":
      handleArrowUp(id);
      break;
    case "Enter":
      handleEnter(id);
      break;
  }
}

function handleArrowDown(id) {
  const results = document.querySelector(`#${id}-results`);
  if (!results) {
    return;
  }

  const active = document.querySelector(`#${id}-results .active`);
  if (!active) {
    results.firstChild.classList.add("active");
  } else if (active?.nextElementSibling) {
    active.classList.remove("active");
    active.nextElementSibling.classList.add("active");
  }
}

function handleArrowUp(id) {
  const results = document.querySelector(`#${id}-results`);
  if (!results) {
    return;
  }

  const active = document.querySelector(`#${id}-results .active`);
  if (!active) {
    results.lastChild.classList.add("active");
  } else if (active?.previousElementSibling) {
    active.classList.remove("active");
    active.previousElementSibling.classList.add("active");
  }
}

function handleEnter(id) {
  const active = document.querySelector(`#${id}-results .active`);
  if (active) {
    document.querySelector(`#${id} input`).value = active.innerText;
    clearResults(id);
  }
}

async function autocomplete(id) {
  const searchInput = document.querySelector(`#${id} input`);
  const query = searchInput.value;

  if (!query) {
    clearResults(id);
    return;
  }

  const response = await fetch(`/products/search?q=${query}`);
  const data = await response.json();

  if (!data.success || data.data.query !== searchInput.value) {
    return;
  }

  const results = document.createElement("div");
  results.id = `${id}-results`;
  clearResults(id);
  results.classList.add("autocomplete-results");
  document.querySelector(`#${id}`).appendChild(results);

  for (const product of data.data.results) {
    const result = document.createElement("div");
    result.innerText = product.name;
    result.addEventListener("click", () => {
      searchInput.value = product.name;
      clearResults(id);
    });
    results.appendChild(result);
  }

  document
    .getElementById(id)
    .addEventListener("keydown", (e) => handleKeydown(id, e));
}
