export const useIsMobile = () => {
    const userAgent = process.server
        ? useRequestHeaders()['user-agent'] || ''
        : navigator.userAgent

    const isMobile = /Mobi|Android|iPhone|iPod|iPad/i.test(userAgent)

    return isMobile
}