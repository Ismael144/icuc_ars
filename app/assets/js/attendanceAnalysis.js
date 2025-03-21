class AttendanceAnalysisController {
    // All methods will be ran here
    constructor(domain) {
        this.domain = domain
        // ...
    }

    getArgs(argName) {
        // ...
    }

    fetchData() {
        response = this.apiFetcher(`${this.domain}/api/attendance/analysis/index`)
        return response
    }

    async apiFetcher(url, additionalArgs) {
        const asyncRequest = await fetch(url, additionalArgs)
        const response = await asyncRequest.json()
        return response 
    }

    getAttendanceRates() { 
        
    }
}