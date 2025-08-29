@extends('layouts.app')
@section('title', 'Media Collection')
@section('content')

<div class="app-content content">
    <div class="content-overlay"></div>
    <div class="header-navbar-shadow"></div>
    <div class="content-wrapper container-xxl p-0">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-start mb-0">View Media</h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="content-body">
            <section id="media-grid">
                <div class="row" id="media-container"></div>
                <div class="d-flex justify-content-center">
                    <ul class="pagination" id="pagination"></ul>
                </div>
            </section>
        </div>
    </div>
</div>

@endsection

@section('pagescript')
<script type="application/javascript">
    'use strict';
    const URL = "{{ route('customermedia') }}";

    function loadMedia(page = 1) {
        $.ajax({
            url: URL + '?page=' + page,
            type: 'GET',
            success: function(response) {
                let mediaHtml = '';
                response.data.forEach((item) => {
                    let buttonText = item.link && item.link.includes('youtube') ? 'View Video' : 'Visit Link';
                    mediaHtml += `
                        <div class="col-md-4 mb-1">
                            <div class="card shadow-sm">
                                <div class="card-body text-center">
                                    ${item.image ? item.image : '<p>No Image</p>'}
                                    <h5 class="mt-1">${item.title}</h5>
                                    <p style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">${item.description || '&nbsp;'}</p>

                                    <a href="${item.link}" target="_blank" class="btn btn-sm btn-primary">${buttonText}</a>
                                </div>
                            </div>
                        </div>`;
                });

                $('#media-container').html(mediaHtml);

                let paginationHtml = '';
                for (let i = 1; i <= response.last_page; i++) {
                    paginationHtml += `<li class="page-item ${i === response.current_page ? 'active' : ''}">
                        <a class="page-link" href="javascript:void(0)" onclick="loadMedia(${i})">${i}</a>
                    </li>`;
                }
                $('#pagination').html(paginationHtml);
            }
        });
    }

    $(document).ready(function() {
        loadMedia();
    });
</script>
@endsection
