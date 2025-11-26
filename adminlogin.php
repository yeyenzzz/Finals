<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>eTapPay</title>
    <link rel="stylesheet" href="style.css" />
  </head>

  <body>
    <div class="page">
      <div class="left">
        <div class="quote">
          <h1>Empowering your</h1>
          <h1>digital banking</h1>
          <h1>experience</h1>
          <p>Secure. Seamless. Smart</p>
          <div class="img">
            <img class="card" src="images/card.png" alt="" />
            <img class="atm" src="images/atm.png" alt="" />
          </div>
        </div>
      </div>
      <div class="wrap">
        <div class="container" id="loginContainer">
          <div class="header">
            <div class="logo">
              <img src="images/logo.png" alt="" class="logo" />
              <h3>eTapPay</h3>
            </div>
            <h2>Welcome Back</h2>
            <p>Login to manage your secure transactions</p>
          </div>
          <form method="POST" action="index.php">
            <div class="inputs">
              <div class="email">
                <input placeholder="Email" name="email" required />
              </div>
              <div class="password">
                <input
                  type="password"
                  placeholder="Password"
                  name="password"
                  required
                />
                <span class="error"></span>
              </div>
              <div class="Button">
                <button type="submit">Login</button>
              </div>
              <div class="paragraph">
                <p>
                  hindi ka admin tol?
                  <a href="index.php">Login here</a>
                </p>
              </div>
            </div>
          </form>
        </div>
      </div>
          </form>
        </div>
      </div>
    </div>
    <script src="script.js"></script>
    
  </body>
</html>
