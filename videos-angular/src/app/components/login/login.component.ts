import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from 'src/app/services/user.service';
import { Router, ActivatedRoute, Params } from '@angular/router';

@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {

  public pageTitle: string;
  public user: User;
  public identity: any;
  public status: string;
  public token: any;

  constructor(private _userService: UserService, private _router: Router, private _route: ActivatedRoute) {
    this.pageTitle = "Login";
    this.status = '';
    this.user = new User(1, '', '', '', '', 'ROLE_USER', '');
  }

  ngOnInit(): void {
    this.logout();
  }

  onSubmit(form: any) {
    this._userService.signUp(this.user).subscribe(
      responseUser => {
        if (!responseUser.status || responseUser.status != 'error') {
          this.status = 'success';
          this.identity = responseUser;

          //Sacar token
          this._userService.signUp(this.user, true).subscribe(
            response => {
              if (!response.status || response.status != 'error') {
                this.token = response;
                console.log(this.identity);
                console.log(this.token);

                localStorage.setItem('token', this.token);
                localStorage.setItem('identity', JSON.stringify(this.identity));

                this._router.navigate(['/inicio']);

              } else {
                this.status = 'error';
              }
            },
            error => {
              this.status = 'error'
              console.log('error')
            }
          )
        } else {
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error'
        console.log('error')
      }
    )
  }

  logout() {
    this._route.params.subscribe(params => {
      let sure = +params['sure'];

      if (sure == 1) {
        localStorage.removeItem('identity');
        localStorage.removeItem('token');

        this.identity = null;
        this.token = null;

        this._router.navigate(['/inicio']);
      }
    });
  }
}
