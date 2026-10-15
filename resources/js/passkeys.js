/**
 * Minimal WebAuthn client for the laravel/passkeys endpoints.
 *
 * Options arrive as JSON with base64url-encoded binary fields and credentials
 * go back the same way, so this only converts between that wire format and
 * the ArrayBuffers navigator.credentials expects.
 */

const base64UrlToBuffer = (value) => {
    const base64 = value.replace(/-/g, '+').replace(/_/g, '/')
    const padded = base64 + '='.repeat((4 - (base64.length % 4)) % 4)

    return Uint8Array.from(atob(padded), (character) => character.charCodeAt(0)).buffer
}

const bufferToBase64Url = (buffer) => {
    let binary = ''
    new Uint8Array(buffer).forEach((byte) => (binary += String.fromCharCode(byte)))

    return btoa(binary).replace(/\+/g, '-').replace(/\//g, '_').replace(/=+$/, '')
}

const decodeDescriptors = (descriptors = []) =>
    descriptors.map((descriptor) => ({ ...descriptor, id: base64UrlToBuffer(descriptor.id) }))

const toCreationOptions = (options) =>
    window.PublicKeyCredential.parseCreationOptionsFromJSON
        ? window.PublicKeyCredential.parseCreationOptionsFromJSON(options)
        : {
              ...options,
              challenge: base64UrlToBuffer(options.challenge),
              user: { ...options.user, id: base64UrlToBuffer(options.user.id) },
              excludeCredentials: decodeDescriptors(options.excludeCredentials),
          }

const toRequestOptions = (options) =>
    window.PublicKeyCredential.parseRequestOptionsFromJSON
        ? window.PublicKeyCredential.parseRequestOptionsFromJSON(options)
        : {
              ...options,
              challenge: base64UrlToBuffer(options.challenge),
              allowCredentials: decodeDescriptors(options.allowCredentials),
          }

const credentialToJson = (credential) => {
    if (typeof credential.toJSON === 'function') {
        return credential.toJSON()
    }

    const response = credential.response
    const encoded = { clientDataJSON: bufferToBase64Url(response.clientDataJSON) }

    if (response.attestationObject) {
        encoded.attestationObject = bufferToBase64Url(response.attestationObject)
        encoded.transports = response.getTransports?.() ?? []
    }

    if (response.authenticatorData) {
        encoded.authenticatorData = bufferToBase64Url(response.authenticatorData)
        encoded.signature = bufferToBase64Url(response.signature)
        encoded.userHandle = response.userHandle ? bufferToBase64Url(response.userHandle) : null
    }

    return {
        id: credential.id,
        rawId: bufferToBase64Url(credential.rawId),
        type: credential.type,
        authenticatorAttachment: credential.authenticatorAttachment ?? null,
        clientExtensionResults: credential.getClientExtensionResults(),
        response: encoded,
    }
}

const defaultMessages = {
    cancelled: 'The passkey request was cancelled or timed out.',
    alreadyRegistered: 'This passkey is already registered to your account.',
    failed: 'Something went wrong with your passkey. Please try again.',
}

const friendlyError = (error, ceremony, messages) => {
    if (error?.name === 'NotAllowedError' || error?.name === 'AbortError') {
        return messages.cancelled
    }

    if (error?.name === 'InvalidStateError' && ceremony === 'create') {
        return messages.alreadyRegistered
    }

    return error?.serverMessage || messages.failed
}

const requestJson = async (url, { method = 'GET', body = null, csrf = null } = {}) => {
    const response = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
        },
        body: body ? JSON.stringify(body) : null,
    })

    // Signed-in visitors hitting a guest-only endpoint are redirected to an
    // HTML page; follow it instead of failing on a body that is not JSON.
    if (response.redirected) {
        window.location.assign(response.url)

        // Never settles, so the button keeps its busy state while the page navigates.
        return new Promise(() => {})
    }

    const payload = await response.json().catch(() => ({}))

    if (! response.ok) {
        const error = new Error('Passkey request failed')
        error.serverMessage = payload.errors ? Object.values(payload.errors).flat()[0] : null

        throw error
    }

    return payload
}

const Passkeys = {
    supported() {
        return typeof window.PublicKeyCredential === 'function' && !! navigator.credentials
    },

    /**
     * Run an assertion ceremony: fetch request options, ask the authenticator,
     * post the signed credential back. Resolves with the server's JSON reply.
     */
    async verify({ optionsUrl, verifyUrl, csrf, remember = false, messages = {} }) {
        try {
            const { options } = await requestJson(optionsUrl)
            const credential = await navigator.credentials.get({ publicKey: toRequestOptions(options) })

            return await requestJson(verifyUrl, {
                method: 'POST',
                csrf,
                body: { credential: credentialToJson(credential), remember },
            })
        } catch (error) {
            throw new Error(friendlyError(error, 'get', { ...defaultMessages, ...messages }))
        }
    },

    /**
     * Run a registration ceremony against creation options the server already
     * issued and resolve with the credential in its JSON wire format.
     */
    async create(options, { messages = {} } = {}) {
        try {
            const credential = await navigator.credentials.create({ publicKey: toCreationOptions(options) })

            return credentialToJson(credential)
        } catch (error) {
            throw new Error(friendlyError(error, 'create', { ...defaultMessages, ...messages }))
        }
    },
}

window.Passkeys = Passkeys

export default Passkeys
