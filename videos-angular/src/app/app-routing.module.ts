import { ModuleWithProviders, NgModule } from '@angular/core';
import { RouterModule, Routes } from '@angular/router';
import { LoginComponent } from './components/login/login.component';
import { RegisterComponent } from './components/register/register.component';
import { HomeComponent } from './components/home/home.component';
import { ErrorComponent } from './components/error/error.component';
import { UserEditComponent } from './components/user-edit/user-edit.component';
import { VideoNewComponent } from './components/video-new/video-new.component';
import { IdentityGuard } from './services/identity.guard';
import { VideoEditComponent } from './components/video-edit/video-edit.component';
import { VideoDetailComponent } from './components/video-detail/video-detail.component';


const routes: Routes = [
  { path: '', component: HomeComponent },
  { path: 'inicio', component: HomeComponent },
  { path: 'inicio/:page', component: HomeComponent },
  { path: 'logout/:sure', component: LoginComponent },
  { path: 'login', component: LoginComponent },
  { path: 'register', component: RegisterComponent },
  { path: 'ajustes', component: UserEditComponent, canActivate: [IdentityGuard] },
  { path: 'guardar-favorito', component: VideoNewComponent, canActivate: [IdentityGuard] },
  { path: 'editar-favorito/:id', component: VideoEditComponent, canActivate: [IdentityGuard] },
  { path: 'video/:id', component: VideoDetailComponent, canActivate: [IdentityGuard] },
  { path: 'error', component: ErrorComponent },
  { path: '**', component: ErrorComponent },
];

export const appRoutingProviders: any[] = [];
export const routing: ModuleWithProviders<any> = RouterModule.forRoot(routes);


@NgModule({
  imports: [RouterModule.forRoot(routes)],
  exports: [RouterModule]
})
export class AppRoutingModule { }
