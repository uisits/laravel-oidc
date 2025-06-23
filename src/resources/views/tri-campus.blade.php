<html>
<head>
    <title>Discovery page</title>
    <script type="text/javascript">
        function redirectPage(use, rname) {
            var isChecked = false;
            console.log("redirect page");
            console.log ("use = " + use);
            console.log("rname = " + rname);

            for (var val = 0, r1 = use.elements; val < r1.length; val++) {
                if (r1[val].name == rname && r1[val].checked) {
                    console.log("r1[val].value = " + r1[val].value);
                    console.log("rname = " + rname);
                    use.action = r1[val].value;
                    isChecked = true;
                }
            }
            console.log("isChecked = " + isChecked);
            if (!isChecked) {
                document.getElementById('errorMessage').style.visibility = 'visible';
                event.preventDefault();
                //return isChecked;
            } else {
                document.getElementById('errorMessage').style.visibility = 'hidden';
            }
        }
    </script>

    <style>
        header {
            display: block;
        }

        #customheader img {
            max-width: 50%;
        }

        #Image1 img {
            overflow-clip-margin: content-box;
            overflow: clip;
        }
        #customheader {
            background-color: white;
            text-align: center;
            padding-top: 10px;
            padding-bottom: 10px;
            border-bottom: #003d7d solid 3px;
        }
    </style>
</head>
<body>
<header id="customheader" role="banner">
    <img id="Image1" src="images/Illinois-System-Logo.svg" alt="Illinois logo" />
</header>
<h1>Select Your University</h1>
<p class="text">
    This service, <span class="serviceName">Cloud Dashboard</span>,
    supports multiple groups associated with the University of Illinois
    System. Select one of the following to go to the appropriate login
    screen.
</p>
<div>
    <form action="{{ route('tri-campus-discovery.update') }}" method="post">
        <fieldset class="no-border">
            <legend>
                <h2>Choose from the following:</h2>
            </legend>
            <p>
                <input type="radio" name="campus" value="/oauth2/authorization/uic">University
                of Illinois Chicago</input>
            </p>
            <p>
                <input type="radio" name="campus" value="/oauth2/authorization/uis">University
                of Illinois Springfield</input>
            </p>
            <p>
                <input type="radio" name="campus"
                       value="/oauth2/authorization/uiuc">University of Illinois
                Urbana-Champaign</input>
            </p>
            <p>
                <button type="submit">Select</button>
                <span id="errorMessage" style="visibility:hidden;color:red;font-weight:bold"> Please pick a campus before clicking select button.</span>
                <!-- Value permanent must be a number which is equivalent to the days the cookie should be valid -->
                <!-- input type="checkbox" name="permanent" id="rememberPermanent"
                        value="3650"> <label for="rememberPermanent">Remember
                        my choice</label> -->
            </p>
        </fieldset>
    </form>
</div>
</body>
</html>