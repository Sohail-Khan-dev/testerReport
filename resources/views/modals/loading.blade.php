<style>
    @keyframes circle-fade {
        0%, 100% {
            opacity: 0.2;
        }
        50% {
            opacity: 1;
        }
    }
    .loading-circle {
        animation: circle-fade 1s infinite;
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
    .loader {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #3498db;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
</style>

<div class="modal fade" id="loadingModal" tabindex="-1" aria-labelledby="loadingModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <div class="loader mx-auto" style="width: 70px;height:70px;"></div>
                    <!-- <img src="images/loadingcircle.svg" alt="svg" srcset="loading"> -->
                </div>
                <div id="loadingSvg" class="d-flex p-7">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20">
                        <circle cx="20" cy="10" r="4" fill="#3498db" class="loading-circle circle-1" />
                        <circle cx="50" cy="10" r="4" fill="#3498db" class="loading-circle circle-2" />
                        <circle cx="80" cy="10" r="4" fill="#3498db" class="loading-circle circle-3" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>