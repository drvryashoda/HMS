document.addEventListener('DOMContentLoaded', () => {
  const addMedicineButton = document.getElementById('add-medicine');
  const medicineList = document.getElementById('medicine-list');
  const addInvestigationButton = document.getElementById('add-investigation');
  const investigationList = document.getElementById('investigation-list');

  const bindRemove = (container) => {
    container.querySelectorAll('.remove-row').forEach((btn) => {
      btn.addEventListener('click', () => {
        const row = btn.closest('.medicine-row, .investigation-row');
        if (row) {
          if (container.children.length === 1) {
            row.querySelectorAll('input').forEach((input) => (input.value = ''));
          } else {
            row.remove();
          }
        }
      });
    });
  };

  if (medicineList) {
    bindRemove(medicineList);
  }
  if (investigationList) {
    bindRemove(investigationList);
  }

  if (addMedicineButton) {
    addMedicineButton.addEventListener('click', () => {
      const template = document.createElement('div');
      template.className = 'row g-2 medicine-row';
      template.innerHTML = `
        <div class="col-md-4">
          <input type="text" class="form-control" name="medicine_name" placeholder="Medicine" />
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control" name="medicine_dosage" placeholder="Dosage" />
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" name="medicine_frequency" placeholder="Frequency" />
        </div>
        <div class="col-md-2">
          <input type="text" class="form-control" name="medicine_duration" placeholder="Duration" />
        </div>
        <div class="col-md-1 d-flex align-items-center">
          <button type="button" class="btn btn-link text-danger remove-row">&times;</button>
        </div>
      `;
      medicineList.appendChild(template);
      bindRemove(medicineList);
    });
  }

  if (addInvestigationButton) {
    addInvestigationButton.addEventListener('click', () => {
      const template = document.createElement('div');
      template.className = 'row g-2 investigation-row';
      template.innerHTML = `
        <div class="col-md-6">
          <input type="text" class="form-control" name="investigation_name" placeholder="Investigation" />
        </div>
        <div class="col-md-1 d-flex align-items-center">
          <button type="button" class="btn btn-link text-danger remove-row">&times;</button>
        </div>
      `;
      investigationList.appendChild(template);
      bindRemove(investigationList);
    });
  }
});
