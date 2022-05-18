import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from 'src/app/services/user.service';
import { Video } from 'src/app/models/video';
import { VideoService } from 'src/app/services/video.service';

@Component({
  selector: 'app-video-edit',
  templateUrl: '../video-new/video-new.component.html',
  styleUrls: ['./video-edit.component.css'],
  providers: [UserService, VideoService]
})
export class VideoEditComponent implements OnInit {

  public page_title: string;
  public identity: any;
  public token: any;
  public video: Video;
  public status: any;

  constructor(private _route: ActivatedRoute, private _router: Router, private _userService: UserService, private _videoService: VideoService) {
    this.page_title = 'Modificar este video'
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.video = new Video(1, this.identity.sub, '', '', '', '', null, null);
  }

  ngOnInit(): void {

    this.getVideo();
  }

  getVideo() {
    this._route.params.subscribe(params => {
      var id = +params['id'];
      this._videoService.getVideo(this.token, id).subscribe(
        response => {
          if (response.status == 'success') {
            this.video = response.video;
          } else {
            this._router.navigate(['/inicio']);
          }
        },
        error => {
          console.log(error);
          this.status = error;
        }
      );
    })
  }

  onSubmit(form: any) {
    this._videoService.update(this.token, this.video, this.video.id).subscribe(
      response => {
        if (response.status = 'success') {
          this.status = 'success';
          this._router.navigate(['/inicio']);
        } else {
          this.status = 'error';
        }
        console.log(response);
      },
      error => {
        console.log(error);
      }
    );
  }

}
