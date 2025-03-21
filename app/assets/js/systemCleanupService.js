function selector(idName) {
    return document.getElementById(idName);
}

const checkupBtn = selector("run_checkup")
const scdServiceContainer = selector("scd-service")

async function systemCleanupService() {
    scdServiceContainer.innerHTML = `
    <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
        <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
            <div class="spinner-border text-success" style="width: 100px; height: 100px;"></div>
            <div class="text-dark mt-1" style="font-weight: bold;">Loading, Please Wait ...</div>
        </div>
    </div>
`

    const request = await fetch("/icuc_ars/api/system_cleanup/diagnosis", {
        method: "GET"
    })

    if (request.status != 200) {
        scdServiceContainer.innerHTML = `
            <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
                <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
                    <div class="text-white py-2 px-3 bg-danger" style="border-radius: 50%;">
                        <i class="fas fa-times" style="font-size: 100px;"></i>
                    </div>
                    <div class="text-dark mt-1" style="font-weight: bold;">An Error Occured, Couldn't diagnose system, try <a href="">refreshing the page</a>...</div>
                </div>
            </div>
        `

        return;
    }

    const res = await request.json()

    const numberOfTrashFound = res["dangling_images_for_users"] + res["dangling_images_for_staff_data_count"] + res["dangling_staff_records_count"]

    scdServiceContainer.innerHTML = `
        <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
            <div>
                <i class="bx bx-trash text-success" style="font-size: 70px;"></i>
            </div>
            <div class="content my-4">
                <b>Found <span class='text-danger'>${numberOfTrashFound} Trash Items(Database Records, Images)</span> To Cleanup</b>
            </div>
            <div>
                <button class="btn btn-success" id="cleanup_btn">Cleanup</button>
            </div>
        </div>
    `

    const cleanupBtn = selector("cleanup_btn")

    cleanupBtn.onclick = () => {
        runCheckup()
    }
}

async function runCheckup() {
    scdServiceContainer.innerHTML = `
        <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
            <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
                <div class="spinner-border text-success" style="width: 100px; height: 100px;"></div>
                <div class="text-dark mt-1" style="font-weight: bold;">Loading, Please Wait ...</div>
            </div>
        </div>
    `

    const request = await fetch("/icuc_ars/api/system_cleanup/cleanup", {
        method: "GET"
    })

    if (request.status != 200) {
        scdServiceContainer.innerHTML = `
            <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
                <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
                    <div class="text-white py-2 px-3 bg-danger" style="border-radius: 50%;">
                        <i class="fas fa-times" style="font-size: 100px;"></i>
                    </div>
                    <div class="text-dark mt-1" style="font-weight: bold;">An Error Occured, Couldn't diagnose system, try <a href="">refreshing the page</a>...</div>
                </div>
            </div>
        `

        return;
    }

    const res = await request.json()

    if (res["status"]) {
        scdServiceContainer.innerHTML = `
            <div class="diagnosis-container d-flex align-items-center justify-content-center flex-column my-3" style="height: 400px; width: 100%; background: #eee; border-radius: 20px;">
                <div class="loader-container d-flex align-items-center justify-content-center flex-column gap-3" style="height: 300px;">
                    <div class="text-white py-2 px-3 bg-success" style="border-radius: 50%;">
                        <i class="fas fa-check" style="font-size: 100px;"></i>
                    </div>
                    <div class="text-grey text-center mt-1" style="font-weight: bold;">Hooray, the system cleanup service ran successfully <br> Go back to the <a href="../dashboard">dashboard page</a>...</div>
                </div>
            </div>
        `
    }

}

checkupBtn.onclick = async () => {
    systemCleanupService()
}
