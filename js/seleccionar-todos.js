const checkboxTodos = document.querySelector('input[name="cb_all"]');

if (checkboxTodos) {
    checkboxTodos.addEventListener("click", () => {
        document.querySelectorAll('input[id^="cb_"]').forEach((checkbox) => {
            checkbox.checked = checkboxTodos.checked;
        });
    });
}
