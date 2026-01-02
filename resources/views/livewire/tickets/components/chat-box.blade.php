<div class="col-12 chat-expand-col {{ $chatExpanded ? 'expanded' : '' }}">
  <div class="card h-100">
    <div class="card-header position-relative border-bottom border-1">
      <div class="d-flex justify-content-between align-items-center flex-wrap">
        <div>
          <h5 class="mb-1 fw-semibold">{{ $customer['name'] }}</h5>
          <small class="text-body-secondary">{{ $customer['email'] }}</small>
        </div>
        <div class="d-flex align-items-center gap-1">
          <button type="button" class="btn btn-text-secondary btn-icon rounded-pill" wire:click="toggleSearch">
            <i class="icon-base ti {{ $searchVisible ? 'tabler-x' : 'tabler-search' }} icon-22px"></i>
          </button>
          <button type="button" class="btn btn-text-secondary btn-icon rounded-pill" wire:click="toggleExpand">
            <i
              class="icon-base ti {{ $chatExpanded ? 'tabler-arrows-minimize' : 'tabler-arrows-maximize' }} icon-22px"></i>
          </button>
        </div>
      </div>
      {{-- Search messages input --}}
      @if ($searchVisible)
        <div class="position-absolute top-5 start-0 end-0 w-full bg-white" style="padding: 0 1rem; bottom: -30px;">
          <div class="input-group input-group-merge">
            <input type="text" class="form-control" placeholder="Search..." aria-label="John Doe"
              wire:model.live="searchTerm">
            <button @class(['input-group-text']) type="button" wire:click="clearSearch">
              <i class="icon-base ti tabler-x"></i>
            </button>
          </div>
          @if (strlen($searchTerm) >= 3)
            <div class="text-success mt-1">Found {{ count($this->filteredMessages) }} message(s)</div>
          @endif
        </div>
      @endif
    </div>
    <div class="card-body pb-0 p-0">
      <div class="chat-history-body px-2"
        style="height: 400px; overflow-y: auto; display: flex; flex-direction: column-reverse;">
        <ul class="list-unstyled chat-history mb-0">
          @forelse($this->filteredMessages as $message)
            <li @class([
                'chat-message mt-1 mb-4',
                'chat-message-right' => $this->isAgentMessage($message),
                'message-interal-note bg-warning-subtle p-1 rounded-2' => $this->isInternalNote(
                    $message),
            ]) wire:key="message-{{ $message['id'] }}">
              <div @class([
                  'd-flex',
                  'justify-content-end' => $this->isAgentMessage($message),
                  'justify-content-start' => !$this->isAgentMessage($message),
              ])>
                <div class="chat-message-wrapper" style="max-width: 70%">
                  <div @class([
                      'chat-message-text px-3 py-2 rounded-3',
                      'border-0 text-body' => $this->isAgentMessage($message),
                      'bg-white border shadow-xs' => !$this->isAgentMessage($message),
                  ]) @style($this->isAgentMessage($message) ? 'background-color: #d8f8d2' : '')>
                    <div class="mb-0 {{ $this->isAgentMessage($message) ? 'text-body' : '' }}">
                      @php
                        $bodyHtml = $message['body_html'] ?? '';
                        // Remove attachment URLs from body HTML
                        if (!empty($message['attachments'])) {
                            foreach ($message['attachments'] as $attachment) {
                                if (!empty($attachment['url'])) {
                                    $bodyHtml = str_replace($attachment['url'], '', $bodyHtml);
                                }
                            }
                        }
                        // Apply search highlighting
                        $bodyHtml = $this->highlightText($bodyHtml);
                      @endphp
                      {!! $bodyHtml !!}
                    </div>
                    @if (!empty($message['attachments']))
                      <div class="mt-2 d-flex flex-wrap gap-2">
                        @foreach ($message['attachments'] as $index => $attachment)
                          @php
                            $isImage =
                                isset($attachment['content_type']) &&
                                str_starts_with($attachment['content_type'], 'image/');

                            $isPdf =
                                isset($attachment['content_type']) && $attachment['content_type'] === 'application/pdf';

                            $attachmentUrl = $attachment['url'] ?? null;
                          @endphp

                          @if ($isImage && $attachmentUrl)
                            <x-image-thumbnail :url="$attachmentUrl" :name="$attachment['name'] ?? 'Image'" :index="$index" :wire-key="'attachment-' . ($message['id'] ?? 'msg') . '-' . $index"
                              width="96px" height="96px" />
                          @elseif ($attachmentUrl)
                            <x-file-attachment
                              :url="$attachmentUrl"
                              :name="$attachment['name'] ?? 'File'"
                              :content-type="$attachment['content_type'] ?? 'application/octet-stream'"
                              :size="$attachment['size'] ?? null"
                              width="96px"
                              height="96px"
                              wire:key="attachment-{{ $message['id'] ?? 'msg' }}-{{ $index }}" />
                          @else
                            <div wire:key="attachment-{{ $message['id'] ?? 'msg' }}-{{ $index }}">
                              <i class="ti tabler-paperclip"></i>
                              @if ($attachmentUrl)
                                <a href="{{ $attachmentUrl }}" target="_blank"
                                  class="{{ $this->isAgentMessage($message) ? 'text-body' : 'text-primary' }}">
                                  {{ $attachment['name'] ?? 'Attachment' }}
                                </a>
                              @else
                                <small class="{{ $this->isAgentMessage($message) ? '' : 'text-muted' }}">
                                  {{ $attachment['name'] ?? 'Attachment' }}
                                </small>
                              @endif
                            </div>
                          @endif
                        @endforeach
                      </div>
                    @endif
                  </div>
                  <div class="text-body-secondary mt-1 small {{ $this->isAgentMessage($message) ? 'text-end' : '' }}">
                    @if ($this->isAgentMessage($message))
                      <i class="icon-base ti tabler-checks icon-16px text-success me-1"></i>
                    @endif
                    {{ $ticket->parseDateFormat($message['created_datetime']) }}
                  </div>
                </div>
              </div>
            </li>
          @empty
            <li class="text-center text-muted py-4">No messages found for this ticket.</li>
          @endforelse
        </ul>
      </div>
    </div>
    <div class="card-footer bg-transparent border-top pt-3">
      <form class="chat-input-wrapper pt-3" wire:submit.prevent="$parent.sendMessage">
        <div class="mb-2 d-flex gap-2">
          <div class="dropdown">
            <button class="btn btn-outline-dark dropdown-toggle px-2" type="button" data-bs-toggle="dropdown"
              aria-expanded="false">
              <i class="icon-base ti {{ $messageType === 'internal-note' ? 'tabler-note' : 'tabler-mail' }}"></i>
            </button>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item" href="#" wire:click.prevent="$parent.$set('messageType', 'email')">
                  <i class="icon-base ti tabler-mail me-2"></i>Email
                </a></li>
              <li><a class="dropdown-item" href="#"
                  wire:click.prevent="$parent.$set('messageType', 'internal-note')">
                  <i class="icon-base ti tabler-note me-2"></i>Internal Note
                </a></li>
            </ul>
          </div>
          <textarea type="text" class="form-control bg-white rounded-3 px-3 py-3 border-dark" style="flex: 1"
            placeholder="Type here or click Generate Response for an instant reply....." wire:model="$parent.newMessage"></textarea>
        </div>
        <div class="d-flex gap-2">
          <button type="button"
            class="btn btn-outline-dark w-50 d-flex align-items-center justify-content-center py-2 rounded-3"
            wire:click="generateResponse" wire:loading.attr="disabled" wire:target="generateResponse">
            <span wire:loading.remove wire:target="generateResponse">
              <span class="me-1">Generate</span>
              <img src="{{ asset('assets/img/customizer/ai.svg') }}">
            </span>
            <span wire:loading wire:target="generateResponse">
              <span class="me-1">Generating...</span>
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
            </span>
          </button>
          <button type="submit"
            class="btn btn-dark w-50 d-flex align-items-center justify-content-center py-2 rounded-3 text-success">
            <span class="me-1">Send</span>
            <i class="icon-base ti tabler-send icon-16px"></i>
          </button>
        </div>
      </form>
    </div>
  </div>

  @script
    <script>
      $wire.on('messageInputReceived', (event) => {
        // Set the message in the parent component
        $wire.$parent.set('newMessage', event.message);

        // Scroll to bottom of chat
        const chatBody = document.querySelector('.chat-history-body');
        if (chatBody) {
          chatBody.scrollTop = chatBody.scrollHeight;
        }

        // Focus on textarea and move cursor to end
        setTimeout(() => {
          const textarea = document.querySelector('.chat-input-wrapper textarea');
          if (textarea) {
            textarea.focus();
            textarea.setSelectionRange(textarea.value.length, textarea.value.length);
          }
        }, 100);
      });
    </script>
  @endscript
</div>
