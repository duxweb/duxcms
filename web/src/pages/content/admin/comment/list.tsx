import React from 'react'
import { useTranslate, useDelete } from '@refinedev/core'
import { PrimaryTableCol, Link, Popconfirm, Switch, Input } from 'tdesign-react/esm'
import { FilterItem, MediaText, PageTable, SelectAsync } from '@duxweb/dux-refine'

const List = () => {
  const translate = useTranslate()
  const { mutate } = useDelete()

  const columns = React.useMemo<PrimaryTableCol[]>(
    () => [
      {
        colKey: 'id',
        sorter: true,
        sortType: 'all',
        title: 'ID',
        width: 100,
      },
      {
        colKey: 'nickname',
        title: translate('content.comment.fields.user'),
        width: 200,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Image src={row.avatar}></MediaText.Image>
              <MediaText.Title>{row.nickname}</MediaText.Title>
              <MediaText.Desc>{row.tel}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'content',
        title: translate('content.comment.fields.content'),
        minWidth: 200,
        cell: ({ row }) => {
          return (
            <MediaText size='small'>
              <MediaText.Title>{row.title}</MediaText.Title>
              <MediaText.Desc>{row.content}</MediaText.Desc>
            </MediaText>
          )
        },
      },
      {
        colKey: 'created_at',
        title: translate('content.comment.fields.createdAt'),
        sorter: true,
        sortType: 'all',
        width: 200,
      },
      {
        colKey: 'status',
        title: translate('system.user.fields.status'),
        width: 100,
        edit: {
          component: Switch,
          props: {},
          keepEditMode: true,
        },
      },
      {
        colKey: 'link',
        title: translate('table.actions'),
        fixed: 'right',
        align: 'center',
        width: 120,
        cell: ({ row }) => {
          return (
            <div className='flex justify-center gap-4'>
              <Popconfirm
                content={translate('buttons.confirm')}
                destroyOnClose
                placement='top'
                showArrow
                theme='default'
                onConfirm={() => {
                  mutate({
                    resource: 'content.comment',
                    id: row.id,
                  })
                }}
              >
                <Link theme='danger'>{translate('buttons.delete')}</Link>
              </Popconfirm>
            </div>
          )
        },
      },
    ],
    // eslint-disable-next-line react-hooks/exhaustive-deps
    [translate],
  )

  return (
    <PageTable
      columns={columns}
      tabs={[
        {
          label: translate('content.article.tab.all'),
          value: '0',
        },
        {
          label: translate('content.article.tab.published'),
          value: '1',
        },
        {
          label: translate('content.article.tab.unpublished'),
          value: '2',
        },
      ]}
      filterRender={() => {
        return (
          <>
            <FilterItem name='keyword'>
              <Input />
            </FilterItem>
            <FilterItem name='user_id'>
              <SelectAsync
                url='member/user'
                optionRender={(item) => {
                  return `${item.nickname} (${item.tel})`
                }}
                optionLabel='nickname'
                optionValue='id'
                clearable
                filterable
                pagination
              />
            </FilterItem>
          </>
        )
      }}
    />
  )
}

export default List
