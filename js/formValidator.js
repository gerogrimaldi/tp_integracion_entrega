// formValidator.js
function initFormValidator(formId, customRules = {}) {
  const form = document.getElementById(formId);
  if (!form) return;

  function validateField(field) {
    // reset any previous custom validity
    field.setCustomValidity("");

    // run custom rule first so checkValidity() sees customValidity
    const rule = customRules[field.name];
    if (rule) {
      try {
        const resultado = rule(field.value, field);
        if (resultado !== true) {
          field.setCustomValidity(resultado || "Campo inválido");
        } else {
          field.setCustomValidity("");
        }
      } catch (err) {
        console.error("Error en regla personalizada:", err);
        field.setCustomValidity("Error de validación");
      }
    }

    // now evaluate validity (HTML5 + customValidity)
    const valid = field.checkValidity();

    field.classList.toggle("is-invalid", !valid);
    field.classList.toggle("is-valid", valid);

    return valid;
  }

  // realtime
  form.querySelectorAll("input, select, textarea").forEach(field => {
    field.addEventListener("input", () => validateField(field));
    field.addEventListener("change", () => validateField(field));
  });

  // expose a method to validate all fields programmatically
  form.validateAll = () => {
    let allValid = true;
    form.querySelectorAll("input, select, textarea").forEach(field => {
      if (!validateField(field)) allValid = false;
    });
    return allValid;
  };

  // form submit handler (in case someone submits the form normally)
  form.addEventListener("submit", (e) => {
    if (!form.validateAll()) {
      e.preventDefault();
      e.stopPropagation();
      form.classList.add("was-validated");
    }
  });
}
