// utils/loadGoogleMaps.ts
let loadingPromise: Promise<void> | null = null

export function loadGoogleMaps(): Promise<void> {
  if ((window as any).google?.maps?.places) {
    return Promise.resolve()
  }

  if (loadingPromise) return loadingPromise

  loadingPromise = new Promise((resolve) => {
    const script = document.createElement('script')
    script.src =
      `https://maps.googleapis.com/maps/api/js?key=AIzaSyCUw1FZPxK5hwCmjeqNm5SxAoMcJSQFFfk&libraries=places`
    script.async = true
    script.defer = true
    script.onload = () => resolve()
    document.head.appendChild(script)
  })

  return loadingPromise
}
