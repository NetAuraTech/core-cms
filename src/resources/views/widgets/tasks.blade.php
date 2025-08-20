@if(count($failed_jobs) > 0)
    <section class="grid margin-block-end-8">
        <h2 class="heading-2 clr-red-300 flex-group align-items-center">
            {!! icon('warning', 'small') !!} {{ __('core-cms::admin.job.failed') }}
        </h2>
        <div class="card">
            <table class="table">
                <thead>
                <tr>
                    <th>{{ __('core-cms::admin.job.date') }}</th>
                    <th>{{ __('core-cms::admin.job.message') }}</th>
                    <th>{{ __('core-cms::admin.actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($failed_jobs as $job)
                    <tr>
                        <td style="white-space: nowrap">
                            <small>{!! ago(new \Carbon\Carbon($job->failed_at)) !!}</small>
                        </td>
                        <td width="75%">
                            <h4 class="margin-block-end-3"><strong>{{ $job->uuid }}</strong></h4>
                            <p class="clr-red-300"
                               style="font-size: .6rem;">{{ shortened_exception($job->exception) }}</p>
                        </td>
                        <td>
                            <div class="flex-group align-items-center justify-content-flex-end"
                                 style="width: initial">
                                <form action="{{ route('admin.retry_job', $job) }}" method="post">
                                    @csrf
                                    <button class="button padding-0"
                                            data-type="transparent"
                                            title="{{ __('core-cms::admin.job.relaunch.value') }} {{ trans_choice('core-cms::admin.job.value', 1) }}"
                                    >{!! icon('sync', 'small') !!}</button>
                                </form>
                                <form
                                        class="clr-red-300"
                                        action="{{ route('admin.destroy_job', $job) }}"
                                        method="post"
                                        onsubmit="{{'return confirm("' . __('core-cms::admin.job.delete.confirm') . '")' }}">
                                    @csrf
                                    @method('delete')
                                    <button class="button padding-0"
                                            data-type="transparent"
                                            title="{{ __('core-cms::admin.delete.value') }} {{ trans_choice('core-cms::admin.job.value', 1) }}"
                                    >{!! icon('trash', 'small') !!}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </section>
@endif