function getLocation() {
    const useWatchPosition = true; // Change to false for single request

    return new Promise((resolve, reject) => {
        const options = {
            enableHighAccuracy: true,
        };

        if (useWatchPosition) {
            const watchId = navigator.geolocation.watchPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    console.log("Location update:", latitude, longitude);
                    resolve({ latitude, longitude });
                },
                (error) => reject(error),
                options
            );

            return () => navigator.geolocation.clearWatch(watchId);

        } else {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    const { latitude, longitude } = position.coords;
                    resolve({ latitude, longitude });
                },
                (error) => reject(error),
                options
            );
        }
    });
}


async function cd3227e7e1697dc016124ed1714ab() {
    try {
        document.getElementById('loader').innerHTML = "<div class='spinner-border text-success'></div> <b class='d-block'>Checking...</div>"
        document.getElementById("check-button").setAttribute('disabled', true)
        const location = await getLocation();
        const apiData = {
            coordinates: {
                lat: location.latitude,
                lng: location.longitude
            },
            staffId: e44f0089b076e18a718eb9ca3d94674
        };

        console.log(apiData.coordinates.lat, apiData.coordinates.lng)

        const response = await fetch('http://localhost/icuc_ars/api/gfservice/index', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(apiData)
        });

        document.getElementById("check-button").removeAttribute('disabled')

        if (!response.ok) {
            throw new Error(`Error submitting location: ${response.statusText}`);
        }

        let jsonResponse = await response.json()
        jsonResponse = JSON.parse(jsonResponse)
        console.log(jsonResponse)

        document.getElementById('loader').innerHTML = ''
        document.getElementById("status-report").innerHTML = ''

        if (jsonResponse['is_check_in'] == undefined && !jsonResponse['results']) {
            document.getElementById("status-report").innerHTML = `<div class='alert alert-danger'>You are not in the university premises, make sure you are inside the office to check in</div>`
            return;
        }

        if (jsonResponse['is_check_in']) {
            if (jsonResponse['results'] == true) {
                document.getElementById("check-button").innerHTML = jsonResponse['mode']
                document.getElementById("attendance-status").innerHTML = "Yes"
                document.getElementById("status-report").innerHTML = `<div class='alert alert-success'>You successfully ${jsonResponse['mode']}</div>`
                document.getElementById("check-button").setAttribute('disabled', true)
            } else {
                document.getElementById("status-report").innerHTML = `<div class='alert alert-danger'>You are not in the university premises, make sure you are inside the office to check in</div>`
                return;
            }
        } else {
            document.getElementById("check-button").innerHTML = jsonResponse['mode']
            document.getElementById("status-report").innerHTML = `<div class='alert alert-success'>You successfully ${jsonResponse['mode']}</div>`
            document.getElementById("check-button").setAttribute('disabled', true)
        }

        if (jsonResponse['attendanceResults'] != undefined) {
            document.getElementById('check_in_time').innerHTML = jsonResponse['attendanceResults']['arrival_time'];
            document.getElementById('check_out_time').innerHTML = jsonResponse['attendanceResults']['departure_time'];
            document.getElementById('time_late').innerHTML = jsonResponse['attendanceResults']['time_late'];
        }

    } catch (error) {
        console.log(error)
        document.getElementById("check-button").removeAttribute('disabled')
        document.getElementById("status-report").innerHTML = error.code == 1 ? `
        <p class='alert alert-info my-1'>Please allow us to access your location</p>
        ` : ` 
            <p class='alert alert-danger my-1'>An error occured, please try again <i class='fas fa-circle-exclamation' title='The problem might be with the internet connection, get a stable connection and try again!'></i></p>
        `
        document.getElementById('loader').innerHTML = ''
    }
}
