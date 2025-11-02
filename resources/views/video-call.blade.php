<!DOCTYPE html>
<html lang="ar">

<head>
    <meta charset="UTF-8">
    <title>مكالمة فيديو - {{ $room }}</title>
    <script src="https://meet.jit.si/external_api.js"></script>
    <style>
        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
        }

        #jitsi-container {
            width: 100%;
            height: 100vh;
        }
    </style>
</head>

<body>
    <div id="jitsi-container"></div>

    <script>
        const domain = "meet.jit.si";
        const options = {
            roomName: "{{ $room }}",
            width: "100%",
            height: "100%",
            parentNode: document.querySelector('#jitsi-container'),
            configOverwrite: {
                startWithAudioMuted: false,
                startWithVideoMuted: false
            },
            interfaceConfigOverwrite: {
                filmStripOnly: false
            }
        };
        const api = new JitsiMeetExternalAPI(domain, options);
    </script>
</body>

</html>