<!-- Loading Modal Styles -->
<style>
    /* Circle fade animation */
    @keyframes circle-fade {
        0%, 100% {
            opacity: 0.2;
            transform: scale(0.8);
        }
        50% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .loading-circle {
        animation: circle-fade 1s infinite;
        filter: drop-shadow(0 0 2px rgba(52, 152, 219, 0.5));
    }

    .circle-1 {
        animation-delay: 0s;
    }

    .circle-2 {
        animation-delay: 0.3s;
    }

    .circle-3 {
        animation-delay: 0.6s;
    }

    /* Spinner animation */
    .loader {
        border: 5px solid rgba(243, 243, 243, 0.5);
        border-top: 5px solid var(--primary-color, #3498db);
        border-radius: 50%;
        width: 70px;
        height: 70px;
        animation: spin 1s linear infinite;
        box-shadow: 0 0 15px rgba(52, 152, 219, 0.2);
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Modal styling */
    #loadingModal .modal-content {
        background-color: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }

    #loadingModal .modal-body {
        padding: 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    #loadingSvg {
        margin-top: 1.5rem;
        width: 100%;
        justify-content: center;
    }
</style>

<!-- Loading Modal Structure -->
<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body">
                <div class="loader mx-auto"></div>
                <div id="loadingSvg" class="d-flex">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20" width="100" height="20">
                        <circle cx="20" cy="10" r="5" fill="var(--primary-color, #3498db)" class="loading-circle circle-1" />
                        <circle cx="50" cy="10" r="5" fill="var(--primary-color, #3498db)" class="loading-circle circle-2" />
                        <circle cx="80" cy="10" r="5" fill="var(--primary-color, #3498db)" class="loading-circle circle-3" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>