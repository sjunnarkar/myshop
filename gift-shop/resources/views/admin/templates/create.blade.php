@extends('layouts.admin')

@section('title', 'Create Customization Template')

@section('admin_content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Create Customization Template</h1>
        <a href="{{ route('admin.customization-templates.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Templates
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.customization-templates.store') }}" method="POST" id="templateForm">
                @csrf
                
                <div class="mb-3">
                    <label for="name" class="form-label">Template Name</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="form-label">Fields</label>
                    <div id="fieldsContainer">
                        <!-- Fields will be added here dynamically -->
                    </div>
                    <button type="button" class="btn btn-outline-primary mt-2" id="addField">
                        <i class="bi bi-plus-circle"></i> Add Field
                    </button>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary">Create Template</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fieldsContainer = document.getElementById('fieldsContainer');
    const addFieldBtn = document.getElementById('addField');
    let fieldCount = 0;

    // Add new field
    addFieldBtn.addEventListener('click', function() {
        const newField = document.createElement('div');
        newField.innerHTML = `
            <div class="card mb-3 field-item" data-index="${fieldCount}">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0">Field #<span class="field-number">${fieldCount + 1}</span></h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-field">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label">Field Name</label>
                                <input type="text" class="form-control" name="fields[${fieldCount}][name]" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Field Type</label>
                                <select class="form-select" name="fields[${fieldCount}][type]" required>
                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="select">Select</option>
                                    <option value="checkbox">Checkbox</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <label class="form-label">Required</label>
                                <select class="form-select" name="fields[${fieldCount}][required]" required>
                                    <option value="1">Yes</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="options-container" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">Options</label>
                            <button type="button" class="btn btn-sm btn-outline-primary add-option">
                                <i class="bi bi-plus-circle"></i> Add Option
                            </button>
                        </div>
                        <div class="option-items">
                            <div class="input-group mb-2">
                                <input type="text" class="form-control" name="fields[${fieldCount}][options][]" placeholder="Option value">
                                <button type="button" class="btn btn-outline-danger remove-option">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        fieldsContainer.appendChild(newField);
        fieldCount++;
        updateFieldNumbers();
    });

    // Remove field
    fieldsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-field')) {
            e.target.closest('.field-item').remove();
            updateFieldNumbers();
        }
    });

    // Add option
    fieldsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.add-option')) {
            const field = e.target.closest('.field-item');
            const fieldIndex = field.dataset.index;
            const optionsContainer = field.querySelector('.option-items');
            const newOption = document.createElement('div');
            newOption.className = 'input-group mb-2';
            newOption.innerHTML = `
                <input type="text" class="form-control" name="fields[${fieldIndex}][options][]" placeholder="Option value">
                <button type="button" class="btn btn-outline-danger remove-option">
                    <i class="bi bi-trash"></i>
                </button>
            `;
            optionsContainer.appendChild(newOption);
        }
    });

    // Remove option
    fieldsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-option')) {
            e.target.closest('.input-group').remove();
        }
    });

    // Show/hide options container based on field type
    fieldsContainer.addEventListener('change', function(e) {
        if (e.target.name && e.target.name.includes('[type]')) {
            const field = e.target.closest('.field-item');
            const optionsContainer = field.querySelector('.options-container');
            optionsContainer.style.display = e.target.value === 'select' ? 'block' : 'none';
        }
    });

    // Update field numbers
    function updateFieldNumbers() {
        const fields = fieldsContainer.querySelectorAll('.field-item');
        fields.forEach((field, index) => {
            field.querySelector('.field-number').textContent = index + 1;
            field.dataset.index = index;
            const inputs = field.querySelectorAll('input, select');
            inputs.forEach(input => {
                if (input.name) {
                    input.name = input.name.replace(/fields\[\d+\]/, `fields[${index}]`);
                }
            });
        });
    }

    // Add initial field
    addFieldBtn.click();
});
</script>
@endpush
@endsection 