function clearResults(id) {
  if (document.querySelector(`#${id}-results`)) {
    document.querySelector(`#${id}-results`).remove();
  }
}

function handleKeydown(id, e) {
  switch (e.key) {
    case "Escape":
      e.preventDefault();
      clearResults(id);
      break;
    case "ArrowDown":
      e.preventDefault();
      handleArrowDown(id);
      break;
    case "ArrowUp":
      e.preventDefault();
      handleArrowUp(id);
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
    results.firstChild.focus();
  } else if (active?.nextElementSibling) {
    active.classList.remove("active");
    active.nextElementSibling.classList.add("active");
    active.nextElementSibling.focus();
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
    results.lastChild.focus();
  } else if (active?.previousElementSibling) {
    active.classList.remove("active");
    active.previousElementSibling.classList.add("active");
    active.previousElementSibling.focus();
  }
}

async function autocomplete(id, queryAPI, callback) {
  const searchInput = document.querySelector(`#${id} input`);
  const query = searchInput.value;

  if (!query) {
    clearResults(id);
    return;
  }

  const response = await fetch(`${queryAPI}?q=${query}`);
  const data = await response.json();

  if (!data.success || data.data.query !== searchInput.value) {
    return;
  }

  const results = document.createElement("div");
  results.id = `${id}-results`;
  clearResults(id);
  results.classList.add("autocomplete-results");
  document.querySelector(`#${id}`).appendChild(results);

  for (const resultItem of data.data.results) {
    const result = document.createElement("button");
    result.dataset.id = resultItem.id;
    result.innerText = resultItem.name;
    result.addEventListener("click", () => {
      searchInput.value = resultItem.name;
      clearResults(id);
      callback(result, resultItem);
    });
    result.addEventListener("keydown", (e) => {
      if (e.key === "Enter") {
        e.preventDefault();
        searchInput.value = resultItem.name;
        clearResults(id);
        callback(result, resultItem);
      }
    });
    results.appendChild(result);
  }

  document.getElementById(id).onkeydown = (e) => handleKeydown(id, e);
}
