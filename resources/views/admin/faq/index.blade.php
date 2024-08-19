@extends('layouts.master', ['page_title' => 'Dashboard'])
@push('page-css')
    <link rel="stylesheet" href="{{ asset('assets/css/adminpage_css.css') }}">
@endpush
@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content d-flex flex-column flex-column-fluid" id="kt_content">
        <div class="toolbar" id="kt_toolbar">
            <div id="kt_toolbar_container" class="container-fluid d-flex justify-content-end flex-stack">
                <a href="#" class="btn btn-primary hover-elevate-up" data-bs-toggle="modal"
                    data-bs-target="#addNewServiceModal">Add FAQ</a>
            </div>
        </div>
        <section class="card">
            <table class="table table-admin-services">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($faqs as $index => $faq)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $faq->question }}</td>
                            <td>{{ $faq->answer }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editModal" data-id="{{ $faq->id }}"
                                    data-question="{{ $faq->question }}" data-answer="{{ $faq->answer }}">
                                    Edit
                                </button>
                                <form action="{{ route('faq.destroy', $faq->id) }}" method="POST"
                                    style="display: inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No categories available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </section>

        <div class="modal fade" id="addNewServiceModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h1 class="modal-title fs-5" id="exampleModalLabel">New FAQ</h1>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('faq.save') }}" method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Question</label>
                                <input type="text" class="form-control" name="question" id="exampleFormControlInput1">
                            </div>
                            <div class="mb-3">
                                <label for="exampleFormControlInput1" class="form-label">Answer</label>
                                <textarea class="form-control" name="answer" cols="30" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="editForm" action="{{ route('faq.save', 0) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="editModalLabel">Edit FAQ</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="editQuestion" class="form-label">Question</label>
                                <input type="text" name="question" id="editQuestion" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label for="editAnswer" class="form-label">Answer</label>
                                <textarea name="answer" id="editAnswer" class="form-control"></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Save changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection


<script>
    document.addEventListener('DOMContentLoaded', function() {
        var editModal = document.getElementById('editModal');
        editModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var id = button.getAttribute('data-id');
            var question = button.getAttribute('data-question');
            var answer = button.getAttribute('data-answer');

            var form = document.getElementById('editForm');
            form.action = form.action.replace('/0', '/' + id);

            var modalQuestionInput = form.querySelector('#editQuestion');
            var modalAnswerTextarea = form.querySelector('#editAnswer');

            modalQuestionInput.value = question;
            modalAnswerTextarea.value = answer;
        });

        editModal.addEventListener('hidden.bs.modal', function(event) {
            var form = document.getElementById('editForm');
            form.action = form.action.replace(/\/\d+$/, '/0');
        });
    });
</script>
